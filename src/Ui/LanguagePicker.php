<?php
namespace OmegaGuardian\ProfilerShield\Ui;

use OmegaGuardian\ProfilerShield\I18n\UiTranslator;
use OmegaGuardian\ProfilerShield\Util\Html;

/** Native disclosure and radio controls: no focus-driven opening or automatic submission. */
final class LanguagePicker
{
    public static function render(UiTranslator $i18n,string $locale,string $view): string
    {
        $t=fn($key)=>Html::e($i18n->trans($key,$locale));
        $html='<details class="language-picker" data-language-picker><summary>'.$t('i18n.language').' · '.Html::e(substr($locale,0,2)).'</summary><form method="get"><input type="hidden" name="view" value="'.Html::e($view).'"><label>'.$t('ui.language_search').'<input type="search" data-language-search autocomplete="off"></label><fieldset class="language-options scroll-region"><legend class="sr-only">'.$t('i18n.language').'</legend>';
        foreach (UiTranslator::AVAILABLE as $code=>$label) {
            $html.='<label data-language-option><input type="radio" name="lang" value="'.$code.'"'.($locale===$code?' checked':'').'><span>'.Html::e($label).'</span></label>';
        }
        return $html.'</fieldset><p data-language-empty hidden>'.$t('ui.no_results').'</p><button type="submit">'.$t('ui.apply_language').'</button></form></details>';
    }
}
