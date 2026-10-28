<?php
namespace OmegaGuardian\ProfilerShield\I18n;

/**
 * Couche minimale d'internationalisation du portail.
 *
 * Format des catalogues : guardian.<locale>.php ; clés stables et courtes.
 * Le traducteur natif de Symfony est privilégié lorsqu'il existe. Le repli
 * local permet au portail d'être utilisable lorsque l'hôte n'a pas activé
 * framework.translator. Aucun appel réseau n'est nécessaire.
 *
 * Les tests contrôlent les catalogues, les paramètres et le rendu des vues.
 */
final class UiTranslator
{
    /** Les 28 pays cibles ; les langues communes partagent leur catalogue. */
    const AVAILABLE = array(
        'fr_FR'=>'FR — Français · France',
        'en_GB'=>'GB — English · United Kingdom',
        'de_DE'=>'DE — Deutsch · Deutschland',
        'es_ES'=>'ES — Español · España',
        'it_IT'=>'IT — Italiano · Italia',
        'pt_PT'=>'PT — Português · Portugal',
        'nl_NL'=>'NL — Nederlands · Nederland',
        'pl_PL'=>'PL — Polski · Polska',
        'ro_RO'=>'RO — Română · România',
        'cs_CZ'=>'CZ — Čeština · Česko',
        'sk_SK'=>'SK — Slovenčina · Slovensko',
        'hu_HU'=>'HU — Magyar · Magyarország',
        'el_GR'=>'GR — Ελληνικά · Ελλάδα',
        'hr_HR'=>'HR — Hrvatski · Hrvatska',
        'ru_RU'=>'RU — Русский · Россия',
        'tr_TR'=>'TR — Türkçe · Türkiye',
        'ar_SA'=>'SA — العربية · السعودية',
        'hi_IN'=>'IN — हिन्दी · भारत',
        'zh_CN'=>'CN — 简体中文 · 中国',
        'ja_JP'=>'JP — 日本語 · 日本',
        'id_ID'=>'ID — Bahasa Indonesia',
        'vi_VN'=>'VN — Tiếng Việt · Việt Nam',
        'ka_GE'=>'GE — ქართული · საქართველო',
        'en_IE'=>'IE — English · Ireland',
        'mn_MN'=>'MN — Монгол · Монгол',
        'ms_MY'=>'MY — Bahasa Melayu · Malaysia',
        'en_US'=>'US — English · United States',
        'en_CA'=>'CA — English · Canada'
    );

    private $translator;
    private $legacyPattern = null;
    private $catalogs = array();

    public function __construct($translator = null)
    {
        $this->translator = $translator;
    }

    /**
     * Accepte un code régional explicite ou une langue navigateur générique.
     * La locale retournée correspond toujours à une option du sélecteur.
     */
    public function normalize($locale)
    {
        $given = str_replace('-', '_', trim((string) $locale));
        if (isset(self::AVAILABLE[$given])) { return $given; }
        $lower = strtolower($given);
        foreach (self::AVAILABLE as $code => $label) {
            if (strtolower($code) === $lower) { return $code; }
        }
        $lang = strtolower(substr($given, 0, 2));
        $preferred = array('fr'=>'fr_FR','en'=>'en_GB','pt'=>'pt_PT','zh'=>'zh_CN');
        if (isset($preferred[$lang])) { return $preferred[$lang]; }
        foreach (self::AVAILABLE as $code => $label) {
            if (strtolower(substr($code, 0, 2)) === $lang) { return $code; }
        }
        return 'fr_FR';
    }

    /** Traduction locale sans envoi de chaînes ni de secrets à un fournisseur externe. */
    public function trans($key, $locale = 'fr_FR', array $parameters = array())
    {
        $locale = $this->normalize($locale);
        $lang = strtolower(substr($locale,0,2));
        if ($this->translator !== null && method_exists($this->translator, 'trans')) {
            try {
                $translated = $this->translator->trans($key, $parameters, 'guardian', $locale);
                if ($translated !== $key) { return $translated; }
            } catch (\Throwable $ignored) {
                // La dépendance au traducteur Symfony n'est jamais bloquante.
            }
        }
        if (!isset($this->catalogs[$lang])) {
            $file = dirname(__DIR__, 2).'/translations/guardian.'.$lang.'.php';
            $this->catalogs[$lang] = is_file($file) ? require $file : array();
        }
        if (!array_key_exists($key,$this->catalogs[$lang]) && $lang !== 'en') {
            return $this->trans($key, 'en_GB', $parameters);
        }
        if (!array_key_exists($key,$this->catalogs[$lang]) && $lang === 'en') {
            return $key;
        }
        return strtr($this->catalogs[$lang][$key], $parameters);
    }

    /** Pour le sélecteur, retourner des libellés de pays explicites. */
    public function locales() { return self::AVAILABLE; }

    /** Translate a static source message; never apply to user-provided content. */
    public function text($source, $locale = 'fr_FR')
    {
        $messages = require __DIR__.'/SourceMessages.php';
        if (isset($messages[$source])) { return $this->trans($messages[$source], $locale); }
        foreach ($messages as $prefix=>$key) {
            if (str_ends_with($prefix, ' : ') && str_starts_with($source, $prefix)) {
                $field=substr($source,strlen($prefix));
                if (preg_match('/^[a-z_]+$/D',$field)) { return $this->trans($key,$locale).$field; }
            }
        }
        return $source;
    }

    /**
     * Traduction des libellés statiques dans les fragments HTML.
     * Les nouveaux composants doivent utiliser des clés stables de traduction.
     * Aucun appel externe. Ne traduit ni CSS/JS ni attributs arbitraires.
     */
    /** Match complete words so a legacy label cannot corrupt "Profiler". */
    private function legacyWords($text, array $catalog)
    {
        if ($this->legacyPattern === null) {
            $keys=array_keys($catalog);
            usort($keys, function ($a, $b) { return strlen($b)-strlen($a); });
            $this->legacyPattern='~(?<![\p{L}\p{N}_])(?:'.implode('|', array_map(function ($key) { return preg_quote($key, '~'); }, $keys)).')(?![\p{L}\p{N}_])~u';
        }
        return preg_replace_callback($this->legacyPattern, function ($match) use ($catalog) { return $catalog[$match[0]]; }, $text);
    }

    public function localizeMarkup($markup, $locale)
    {
        if ($this->normalize($locale) === 'fr_FR') { return $markup; }
        $catalog=[];
        foreach (require __DIR__.'/SourceMessages.php' as $source=>$key) {
            $catalog[$source]=$this->trans($key,$locale);
        }
        // Sépare les éléments CSS/JS afin de ne jamais traduire du code.
        $parts=preg_split('~(<style\b[^>]*>.*?</style>|<script\b[^>]*>.*?</script>)~si', $markup, -1, PREG_SPLIT_DELIM_CAPTURE);
        foreach ($parts as $index=>$piece) {
            if (preg_match('~^<(style|script)\b~i', $piece)) { continue; }
            $piece=preg_replace_callback('~>([^<>]+)<~u', function ($match) use ($catalog) {
                return '>'.$this->legacyWords($match[1],$catalog).'<';
            }, $piece);
            $piece=preg_replace_callback('~\b(placeholder|aria-label|title)="([^"]*)"~u',function ($match) use ($catalog) {
                return $match[1].'="'.$this->legacyWords($match[2],$catalog).'"';
            },$piece);
            $parts[$index]=$piece;
        }
        return implode('', $parts);
    }
}
