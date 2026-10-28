'use strict';
const {chromium}=require('playwright');
const assert=require('node:assert/strict');
const path=require('node:path');
(async()=>{
 const browser=await chromium.launch({headless:true});let checks=0;const errors=[];
 try{
  for(const width of [1440,390]){
   const page=await browser.newPage({viewport:{width,height:900}});page.on('pageerror',e=>errors.push(e.message));
   await page.goto('file://'+path.resolve(process.argv[2],'settings.html'));
   const picker=page.locator('[data-language-picker]');await picker.locator('summary').click();await page.waitForTimeout(300);
   assert(await picker.evaluate(e=>e.open));checks++;
   await picker.locator('[data-language-search]').fill('Deutsch');assert(await picker.evaluate(e=>e.open));checks++;
   assert.equal(await picker.locator('[data-language-option]:visible').count(),1);checks++;
   await picker.locator('[data-language-search]').fill('');
   assert(await picker.locator('.language-options').evaluate(e=>e.scrollHeight>e.clientHeight));checks++;
   await picker.locator('input[value="de_DE"]').check();assert(await picker.evaluate(e=>e.open));checks++;
   await picker.locator('summary').click();assert(!await picker.evaluate(e=>e.open));checks++;
   await page.locator('[data-account-search]').fill('fixture-dev');assert.equal(await page.locator('[data-account-name]:visible').count(),1);checks++;
   await page.locator('[data-account-search]').fill('');await page.locator('[data-account-role]').selectOption('admin');assert.equal(await page.locator('[data-account-name]:visible').count(),1);checks++;
   const lab=page.locator('[data-password-lab]');
   for(const cls of ['lower','upper','digit','symbol']){
    await lab.locator('[data-lab-alphabet="'+cls+'"]').setChecked(cls==='lower');await lab.locator('[data-lab-required="'+cls+'"]').uncheck();
   }
   await lab.locator('[data-lab-length]').fill('19');await page.waitForTimeout(200);
   assert((await lab.locator('[data-lab-results]').innerText()).includes('12 140 000 000 000 000 000'));checks++;
   assert(await page.evaluate(()=>document.documentElement.scrollWidth<=innerWidth));checks++;
   await lab.scrollIntoViewIfNeeded();await page.screenshot({path:'/tmp/guardian-rc9-preview/browser-'+width+'.png'});
   await page.goto('file://'+path.resolve(process.argv[2],'audit.html'));const events=page.locator('.events');
   assert(await events.evaluate(e=>e.scrollHeight>e.clientHeight));checks++;
   assert.equal(await events.locator('.event').count(),50);checks++;
   assert(await page.evaluate(()=>document.documentElement.scrollWidth<=innerWidth));checks++;
   await page.close();
  }
  assert.deepEqual(errors,[]);checks++;console.log(JSON.stringify({checks,browser:'Chromium 141',widths:[1440,390],failures:errors},null,2));
 }finally{await browser.close();}
})().catch(e=>{console.error(e);process.exit(1)});
