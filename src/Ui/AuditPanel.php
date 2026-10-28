<?php
namespace OmegaGuardian\ProfilerShield\Ui;

use OmegaGuardian\ProfilerShield\I18n\UiTranslator;
use OmegaGuardian\ProfilerShield\Util\Html;

final class AuditPanel
{
    /** Filter the whole retained journal before pagination, with bounded HTML output. */
    public static function render(array $entries,array $query,UiTranslator $i18n,string $locale): string
    {
        $t=fn($key)=>Html::e($i18n->trans($key,$locale));
        $scalar=static fn($key)=>isset($query[$key]) && is_string($query[$key]) ? $query[$key] : '';
        $type=$scalar('event_type');$actor=$scalar('actor');$types=[];$actors=[];
        foreach ($entries as $entry) {$types[(string)$entry['event']]=true;$actors[(string)$entry['actor']]=true;}
        ksort($types);ksort($actors);
        $filtered=array_values(array_filter(array_reverse($entries),static fn($e)=>($type==='' || $e['event']===$type) && ($actor==='' || $e['actor']===$actor)));
        $pages=max(1,(int)ceil(count($filtered)/50));
        $page=max(1,min($pages,ctype_digit($scalar('page'))?(int)$scalar('page'):1));
        $html='<section class="panel"><h2>'.$t('legacy.0485c7b886e2').'</h2><p>'.$t('ui.audit_help').'</p><form method="get" class="list-filters"><input type="hidden" name="view" value="audit">';
        foreach (['event_type'=>[$types,$type,'ui.event_type'],'actor'=>[$actors,$actor,'auth.username']] as $name=>[$options,$selected,$label]) {
            $html.='<label>'.$t($label).'<select name="'.$name.'"><option value="">'.$t('ui.all').'</option>';
            // Keep unknown values visible so a stale URL never silently broadens a filter.
            if ($selected!=='' && !isset($options[$selected])) {$options[$selected]=true;}
            foreach ($options as $value=>$unused) {$html.='<option value="'.Html::e($value).'"'.((string)$value===$selected?' selected':'').'>'.Html::e($value===''?'—':$value).'</option>';}
            $html.='</select></label>';
        }
        $html.='<button type="submit">'.$t('ui.filter').'</button><a href="?view=audit">'.$t('ui.reset_filters').'</a></form><p role="status">'.count($filtered).' · '.$t('ui.results').'</p><div class="events scroll-region" tabindex="0" role="region" aria-label="'.$t('nav.audit').'">';
        foreach (array_slice($filtered,($page-1)*50,50) as $entry) {
            $html.='<div class="event"><time>'.Html::e($entry['at']).'</time> · <strong>'.Html::e($entry['event']).'</strong> · '.Html::e($entry['actor']).'</div>';
        }
        if (!$filtered) {$html.='<p>'.$t('ui.no_results').'</p>';}
        $html.='</div><nav class="list-pagination" aria-label="'.$t('ui.pages').'">';
        foreach ([-1=>'ui.previous',1=>'ui.next'] as $offset=>$label) {
            $next=$page+$offset;
            if ($next>=1 && $next<=$pages) {$html.='<a href="?'.Html::e(http_build_query(['view'=>'audit','event_type'=>$type,'actor'=>$actor,'page'=>$next])).'">'.$t($label).'</a>';}
        }
        return $html.'<span>'.$page.' / '.$pages.'</span></nav></section>';
    }
}
