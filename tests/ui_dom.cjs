'use strict';
const {JSDOM}=require('jsdom');
const fs=require('node:fs'),assert=require('node:assert/strict');
const dom=new JSDOM(fs.readFileSync(process.argv[2],'utf8'),{runScripts:'dangerously',url:'https://localhost/synthetic-panel?view=settings'});
const d=dom.window.document;let checks=0;
function check(value,label){assert(value,label);checks++;}
const picker=d.querySelector('[data-language-picker]'),summary=picker.querySelector('summary');
summary.click();check(picker.open,'first language click stays open');
const search=picker.querySelector('[data-language-search]');search.value='Deutsch';search.dispatchEvent(new dom.window.Event('input',{bubbles:true}));
check(picker.open,'typing does not close language panel');check(picker.querySelectorAll('[data-language-option]:not([hidden])').length===1,'language search filters');
search.value='no-such-language';search.dispatchEvent(new dom.window.Event('input'));check(!picker.querySelector('[data-language-empty]').hidden,'language empty state');
summary.click();check(!picker.open,'explicit second click closes');summary.click();check(picker.open,'can reopen');
const accounts=d.getElementById('guardian-accounts');
accounts.querySelector('[data-account-role]').value='developer';accounts.querySelector('[data-account-role]').dispatchEvent(new dom.window.Event('change'));
check(accounts.querySelectorAll('[data-account-name]:not([hidden])').length===1,'account role filter');
const accountSearch=accounts.querySelector('[data-account-search]');accountSearch.value='admin';accountSearch.dispatchEvent(new dom.window.Event('input'));
check(accounts.querySelectorAll('[data-account-name]:not([hidden])').length===0,'account and role intersect');
accounts.querySelector('[data-account-role]').value='';accounts.querySelector('[data-account-role]').dispatchEvent(new dom.window.Event('change'));check(accounts.querySelectorAll('[data-account-name]:not([hidden])').length===1,'account search');
const panel=d.querySelector('[data-auth-safety]'),lab=panel.querySelector('[data-password-lab]');
function render(){panel.dispatchEvent(new dom.window.Event('change',{bubbles:true}));}
function onlineText(){return lab.querySelectorAll('tr')[3].querySelector('td').textContent;}
lab.querySelectorAll('[data-lab-alphabet]').forEach(x=>x.checked=x.dataset.labAlphabet==='lower');
lab.querySelectorAll('[data-lab-required]').forEach(x=>x.checked=false);
lab.querySelector('[data-lab-length]').value='7';render();const seven=onlineText();
check(seven.includes('127'),'seven lowercase characters about 127 years at 1/s');
lab.querySelector('[data-lab-length]').value='19';render();const nineteen=onlineText();
check(!/\d(?:[,.]\d+)?E[+-]?\d/i.test(nineteen),'no ambiguous scientific notation');
check(nineteen.replace(/\D/g,'').length>=19,'nineteen characters shown as full large value');
check(nineteen!==seven,'larger duration distinct');
lab.querySelector('[data-lab-pin]').click();check(lab.querySelectorAll('tr')[0].children.length===3,'comparison pinned');
lab.querySelector('[data-lab-length]').value='128';lab.querySelectorAll('[data-lab-alphabet]').forEach(x=>x.checked=true);render();check(!lab.querySelector('[data-lab-results]').textContent.includes('Infinity'),'maximum scenario remains finite');
lab.querySelector('[data-lab-offline]').value='0';render();check(!lab.querySelector('table'),'invalid input has explicit state');
panel.querySelector('[data-rate-preset="progressive"]').click();check(panel.querySelectorAll('[data-rate-rule]').length===2,'progressive preset');
check(!panel.querySelector('.unsaved-badge').hidden,'unsaved policy indicated');
for(let i=0;i<10;i++)panel.querySelector('[data-add-rule]').click();check(panel.querySelectorAll('[data-rate-rule]').length===8,'maximum eight stages');check(panel.querySelector('[data-add-rule]').disabled,'add disabled at capacity');
console.log(JSON.stringify({checks,failures:[],seven,nineteen},null,2));dom.window.close();
