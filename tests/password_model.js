'use strict';
const assert=require('node:assert/strict');
const model=require('../assets/security.js');
assert.equal(model.combinations(2,{upper:true,digit:true},{upper:true,digit:true}),520n);
assert.equal(model.combinations(3,{lower:true},{}),17576n);
assert.equal(model.combinations(1,{upper:true,digit:true},{upper:true,digit:true}),0n);
assert.equal(model.combinations(10,{lower:true},{upper:true}),0n);
assert.equal(model.combinations(1,{lower:true,upper:true,digit:true,symbol:true},{}),94n);
assert.equal(model.combinations(129,{lower:true},{}),0n);
assert(model.combinations(128,{lower:true,upper:true,digit:true,symbol:true},{})>BigInt(Number.MAX_SAFE_INTEGER));
const rule={attempts:10,block_minutes:10,window_minutes:30,escalation_minutes:30};
assert.equal(model.onlineSeconds(10,1,[rule]),10);
assert.equal(model.onlineSeconds(11,1,[rule]),611);
assert.equal(model.onlineSeconds(21,1,[rule]),1221);
const stages=[{...rule,attempts:5},{...rule,attempts:5,block_minutes:120}];
assert.equal(model.onlineSeconds(11,1,stages),7811);
assert.equal(model.onlineSeconds(21,1,stages),22221);
assert.equal(model.onlineSeconds(100,0.001,[rule]),null);
assert.equal(model.onlineSeconds(100,Infinity,[rule]),null);
for(const rules of [[rule],stages]) {
    for(let n=1;n<=100;n++) {
        let sum=0;for(let rank=1;rank<=n;rank++)sum+=model.onlineSeconds(rank,1,rules);
        assert(Math.abs(model.averageOnlineSeconds(BigInt(n),1,rules)-sum/n)<1e-7);
    }
}
assert(Number.isFinite(model.averageOnlineSeconds(94n**128n,1,stages)));
assert.equal(model.averageOnlineSeconds(10n,1,[{...rule,attempts:1.5}]),null);
console.log('216 independent password-model checks passed, including explicit rank enumeration.');
