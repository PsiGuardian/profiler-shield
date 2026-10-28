<?php
namespace OmegaGuardian\ProfilerShield\Ui;
use OmegaGuardian\ProfilerShield\I18n\UiTranslator;
use OmegaGuardian\ProfilerShield\Util\Html;

final class SecurityPanel
{
    public function __construct(private UiTranslator $i18n) {}
    public function passwordForm(array $cfg,string $csrf,string $locale,bool $forced,string $error=''): string
    {
        $t=fn($key)=>Html::e($this->i18n->trans('security.'.$key,$locale));
        $html='<section class="panel password-rotation" id="guardian-password"><h2>'.$t('change_title').'</h2><p>'.$t($forced?'first_access':'change_help').'</p>';
        if ($error) {$html.='<p class="inline-alert" role="alert">'.Html::e($this->i18n->trans($error,$locale)).'</p>';}
        $html.=$this->requirements($cfg,$locale,'rotation-policy');
        $html.='<form method="post" class="password-change-form"><input type="hidden" name="_csrf" value="'.Html::e($csrf).'"><input type="hidden" name="action" value="password_change">';
        foreach (['current_password'=>['current_password','current-password'],'new_password'=>['new_password','new-password'],'password_confirm'=>['confirmation','new-password']] as $name=>$label) {
            $html.='<label>'.$t($label[0]).'<input type="password" name="'.$name.'" autocomplete="'.$label[1].'" required maxlength="512"></label>';
        }
        $html.='<button type="submit">'.$t('change_submit').'</button></form>';
        if ($forced) {$html.='<form method="post"><input type="hidden" name="action" value="logout"><input type="hidden" name="_csrf" value="'.Html::e($csrf).'"><button type="submit" class="button-secondary">'.$t('logout').'</button></form>';}
        else {$html.='<p><a href="?view=settings">'.Html::e($this->i18n->trans('ui.review_access',$locale)).'</a></p>';}
        return $html.'</section>';
    }
    public function render(array $cfg,string $csrf,string $locale): string
    {
        $t=fn($key)=>Html::e($this->i18n->trans('security.'.$key,$locale));
        $html='<section class="panel auth-safety" id="guardian-auth-safety" data-auth-safety><div class="section-head"><div><span class="section-kicker">'.Html::e($this->i18n->trans('config.section',$locale)).'</span><h2>'.$t('title').'</h2><p>'.$t('intro').'</p></div></div>';
        $html.='<form method="post" class="config-form" data-security-form><input type="hidden" name="action" value="security_policy"><input type="hidden" name="_csrf" value="'.Html::e($csrf).'"><h3>'.$t('throttle_title').'</h3><p>'.$t('throttle_help').'</p>';
        $html.='<div class="security-presets"><button type="button" data-rate-preset="default" class="button-secondary">'.$t('default_rule').'</button><button type="button" data-rate-preset="progressive" class="button-secondary">'.$t('progressive_rule').'</button></div>';
        $html.='<div class="rate-rules" data-rate-rules>';
        foreach ($cfg['login_rules'] as $index=>$r) {$html.=$this->rule($index,$r,$locale);}
        $html.='</div><button type="button" data-add-rule class="button-secondary">+ '.$t('add_rule').'</button><p class="hint">'.$t('rule_help').'</p>';
        $html.='<h3>'.$t('policy_title').'</h3><p>'.$t('policy_help').'</p><label>'.$t('minimum').'<input type="number" name="password_policy[min_length]" value="'.(int)$cfg['password_policy']['min_length'].'" min="8" max="64" required></label><div class="policy-classes">';
        foreach (['upper','lower','digit','symbol'] as $class) {$html.='<label class="check"><input type="checkbox" name="password_policy[require_'.$class.']" value="1"'.($cfg['password_policy']['require_'.$class]?' checked':'').'>'.$t('require_'.$class).'</label>';}
        $html.='</div><p class="hint">'.$t('length_advice').'</p><p class="hint">'.$t(defined('PASSWORD_ARGON2ID')?'hash_argon':'hash_bcrypt').'</p><label class="check"><input type="checkbox" name="force_existing" value="1">'.$t('force_existing').'</label><p class="hint">'.$t('force_help').'</p><label>'.$t('admin_password').'<input type="password" name="password" autocomplete="current-password" required maxlength="512"></label><button type="submit">'.$t('save').'</button></form>';
        $html.='<details class="password-lab" open><summary>'.$t('simulator').'</summary><p>'.$t('simulation_help').'</p><div data-password-lab><div class="lab-inputs"><label>'.$t('sim_length').'<input type="number" data-lab-length min="1" max="128" value="'.(int)$cfg['password_policy']['min_length'].'"></label><label>'.$t('online_rate').'<input type="number" data-lab-online min="0.001" max="1000000000" step="any" value="1"></label><label>'.$t('offline_rate').'<input type="number" data-lab-offline min="0.001" max="1000000000000" step="any" value="10000"></label><label>'.$t('hour_cost').'<input type="number" data-lab-cost min="0" max="1000000" step="any" value="2"></label></div>';
        $html.='<div class="lab-alphabets"><fieldset><legend>'.$t('available_chars').'</legend>';
        foreach (['lower'=>26,'upper'=>26,'digit'=>10,'symbol'=>32] as $class=>$size) {$html.='<label class="check"><input type="checkbox" data-lab-alphabet="'.$class.'" checked>'.$t('class_'.$class).' ('.$size.')</label>';}
        $html.='</fieldset><fieldset><legend>'.$t('required_chars').'</legend>';
        foreach (['upper','lower','digit','symbol'] as $class) {$html.='<label class="check"><input type="checkbox" data-lab-required="'.$class.'"'.($cfg['password_policy']['require_'.$class]?' checked':'').'>'.$t('require_'.$class).'</label>';}
        $html.='</fieldset></div><div class="lab-actions"><button type="button" data-lab-policy class="button-secondary">'.$t('use_policy').'</button><button type="button" data-lab-pin class="button-secondary">'.$t('compare').'</button></div>';
        $messages=[];foreach (['invalid_simulation','window_assumption','scenario','current_scenario','combinations','bits','online_plain','online_locked','offline_time','offline_cost','seconds','years'] as $k) {$messages[$k]=$this->i18n->trans('security.'.$k,$locale);}
        $html.='<div data-lab-results role="status" aria-live="polite" data-lab-messages="'.Html::e(json_encode($messages,JSON_UNESCAPED_UNICODE|JSON_THROW_ON_ERROR)).'"></div><p class="hint">'.$t('simulation_caution').'</p><p class="hint">'.$t('number_help').'</p><p class="hint">'.$t('composition_caution').'</p><noscript><p>'.$t('javascript_required').'</p></noscript></div></details>';
        return $html.'</section>';
    }
    /** Describes the saved policy; shared by account creation and password renewal. */
    public function requirements(array $cfg,string $locale,string $id): string
    {
        $t=fn($key)=>Html::e($this->i18n->trans('security.'.$key,$locale));
        $html='<div class="password-policy-summary" id="'.Html::e($id).'"><strong>'.$t('required_policy').'</strong><ul class="password-requirements"><li>'.$t('minimum').' : '.(int)$cfg['password_policy']['min_length'].'</li>';
        $any=false;
        foreach (['upper','lower','digit','symbol'] as $class) {
            if ($cfg['password_policy']['require_'.$class]) {$any=true;$html.='<li>'.$t('require_'.$class).'</li>';}
        }
        if (!$any) {$html.='<li>'.$t('no_classes').'</li>';}
        return $html.'</ul></div>';
    }
    private function rule(int $index,array $r,string $locale): string
    {
        $t=fn($key)=>Html::e($this->i18n->trans('security.'.$key,$locale));
        $html='<fieldset class="rate-rule" data-rate-rule><legend>'.$t('stage').' <span data-rule-number>'.($index+1).'</span></legend>';
        foreach (['attempts'=>100,'block_minutes'=>10080,'window_minutes'=>1440,'escalation_minutes'=>10080] as $key=>$max) {$html.='<label>'.$t($key).'<input type="number" name="login_rules['.$index.']['.$key.']" data-rule-field="'.$key.'" min="1" max="'.$max.'" value="'.(int)$r[$key].'" required></label>';}
        return $html.'<button type="button" data-remove-rule class="button-secondary">'.$t('remove_rule').'</button></fieldset>';
    }
}
