import{K as Ao,a0 as Ro,o as We,g as qe,a1 as El,r as us,a as Fe,w as Oe,d as _,t as V,b as Be,l as ke,m as Qe,p as en,i as Vt,F as Xn,s as ti,c as Ca,n as Tl}from"./app-B2q-TZWP.js";import{P as wl}from"./PublicLayout-BVkPH1oY.js";import{_ as Al}from"./SeoHead-De0sRyhf.js";import{u as Rl}from"./i18n-CSws7s-2.js";import"./ThemeToggle-CQHZHK_F.js";import"./SupportWidget-CKCRmP-S.js";import"./BrandLogo-DCStvNhU.js";import"./_plugin-vue_export-helper-DlAUqK2U.js";/**
 * @license
 * Copyright 2010-2026 Three.js Authors
 * SPDX-License-Identifier: MIT
 */const Qr="187",Cl=0,Pa=1,Pl=2,fs=1,Ll=2,Ci=3,Jn=0,Xt=1,En=2,mi=0,Li=1,La=2,Da=3,Ia=4,Dl=5,pi=100,Il=101,Nl=102,Ul=103,Fl=104,Ol=200,Bl=201,kl=202,zl=203,Co=204,Po=205,Gl=206,Hl=207,Vl=208,Wl=209,Xl=210,ql=211,Yl=212,Kl=213,Zl=214,cr=0,dr=1,ur=2,Di=3,fr=4,hr=5,pr=6,mr=7,Lo=0,$l=1,Jl=2,wn=0,Do=1,Io=2,No=3,jr=4,Uo=5,Fo=6,Oo=7,Bo=300,xi=301,Ii=302,Ns=303,Us=304,gr=1e3,Tn=1001,xr=1002,Ct=1003,Ql=1004,Vi=1005,Wt=1006,Fs=1007,On=1008,Zt=1009,ko=1010,zo=1011,Ni=1012,ea=1013,gn=1014,hn=1015,xn=1016,ta=1017,na=1018,_i=1020,Go=35902,Ho=35899,Vo=1021,Wo=1022,pn=1023,zn=1026,Bn=1027,Xo=1028,ia=1029,Qn=1030,sa=1031,ra=1033,hs=33776,ps=33777,ms=33778,gs=33779,_r=35840,vr=35841,Sr=35842,Mr=35843,br=36196,yr=37492,Er=37496,Tr=37488,wr=37489,vs=37490,Ar=37491,Rr=37808,Cr=37809,Pr=37810,Lr=37811,Dr=37812,Ir=37813,Nr=37814,Ur=37815,Fr=37816,Or=37817,Br=37818,kr=37819,zr=37820,Gr=37821,Hr=36492,Vr=36494,Wr=36495,Xr=36283,qr=36284,Ss=36285,Yr=36286,jl=3200,Kr=0,ec=1,Fn="",Jt="srgb",Ms="srgb-linear",bs="linear",at="srgb",Os=7680,tc=519,nc=512,ic=513,sc=514,qo=515,rc=516,ac=517,Yo=518,Ko=519,oc=35044,Na="300 es",ln=2e3,Ui=2001;function lc(i){for(let e=i.length-1;e>=0;--e)if(i[e]>=65535)return!0;return!1}function ys(i){return document.createElementNS("http://www.w3.org/1999/xhtml",i)}function cc(){const i=ys("canvas");return i.style.display="block",i}const Ua={};function Fa(...i){const e="THREE."+i.shift();console.log(e,...i)}function Zo(i){const e=i[0];if(typeof e=="string"&&e.startsWith("TSL:")){const t=i[1];t&&t.isStackTrace?i[0]+=" "+t.getLocation():i[1]='Stack trace not available. Enable "THREE.Node.captureStackTrace" to capture stack traces.'}return i}function De(...i){i=Zo(i);const e="THREE."+i.shift();{const t=i[0];t&&t.isStackTrace?console.warn(t.getError(e)):console.warn(e,...i)}}function nt(...i){i=Zo(i);const e="THREE."+i.shift();{const t=i[0];t&&t.isStackTrace?console.error(t.getError(e)):console.error(e,...i)}}function xs(...i){const e=i.join(" ");e in Ua||(Ua[e]=!0,De(...i))}function dc(i,e,t){return new Promise(function(n,s){function r(){switch(i.clientWaitSync(e,i.SYNC_FLUSH_COMMANDS_BIT,0)){case i.WAIT_FAILED:s();break;case i.TIMEOUT_EXPIRED:setTimeout(r,t);break;default:n()}}setTimeout(r,t)})}const uc={[cr]:cr,[ur]:pr,[fr]:fr,[Di]:hr,[dr]:dr,[pr]:ur,[mr]:mr,[hr]:Di};class ei{addEventListener(e,t){this._listeners===void 0&&(this._listeners={});const n=this._listeners;n[e]===void 0&&(n[e]=[]),n[e].indexOf(t)===-1&&n[e].push(t)}hasEventListener(e,t){const n=this._listeners;return n===void 0?!1:n[e]!==void 0&&n[e].indexOf(t)!==-1}removeEventListener(e,t){const n=this._listeners;if(n===void 0)return;const s=n[e];if(s!==void 0){const r=s.indexOf(t);r!==-1&&s.splice(r,1)}}dispatchEvent(e){const t=this._listeners;if(t===void 0)return;const n=t[e.type];if(n!==void 0){e.target=this;const s=n.slice(0);for(let r=0,a=s.length;r<a;r++)s[r].call(this,e);e.target=null}}}const It=["00","01","02","03","04","05","06","07","08","09","0a","0b","0c","0d","0e","0f","10","11","12","13","14","15","16","17","18","19","1a","1b","1c","1d","1e","1f","20","21","22","23","24","25","26","27","28","29","2a","2b","2c","2d","2e","2f","30","31","32","33","34","35","36","37","38","39","3a","3b","3c","3d","3e","3f","40","41","42","43","44","45","46","47","48","49","4a","4b","4c","4d","4e","4f","50","51","52","53","54","55","56","57","58","59","5a","5b","5c","5d","5e","5f","60","61","62","63","64","65","66","67","68","69","6a","6b","6c","6d","6e","6f","70","71","72","73","74","75","76","77","78","79","7a","7b","7c","7d","7e","7f","80","81","82","83","84","85","86","87","88","89","8a","8b","8c","8d","8e","8f","90","91","92","93","94","95","96","97","98","99","9a","9b","9c","9d","9e","9f","a0","a1","a2","a3","a4","a5","a6","a7","a8","a9","aa","ab","ac","ad","ae","af","b0","b1","b2","b3","b4","b5","b6","b7","b8","b9","ba","bb","bc","bd","be","bf","c0","c1","c2","c3","c4","c5","c6","c7","c8","c9","ca","cb","cc","cd","ce","cf","d0","d1","d2","d3","d4","d5","d6","d7","d8","d9","da","db","dc","dd","de","df","e0","e1","e2","e3","e4","e5","e6","e7","e8","e9","ea","eb","ec","ed","ee","ef","f0","f1","f2","f3","f4","f5","f6","f7","f8","f9","fa","fb","fc","fd","fe","ff"],Bs=Math.PI/180,Zr=180/Math.PI;function Oi(){const i=Math.random()*4294967295|0,e=Math.random()*4294967295|0,t=Math.random()*4294967295|0,n=Math.random()*4294967295|0;return(It[i&255]+It[i>>8&255]+It[i>>16&255]+It[i>>24&255]+"-"+It[e&255]+It[e>>8&255]+"-"+It[e>>16&15|64]+It[e>>24&255]+"-"+It[t&63|128]+It[t>>8&255]+"-"+It[t>>16&255]+It[t>>24&255]+It[n&255]+It[n>>8&255]+It[n>>16&255]+It[n>>24&255]).toLowerCase()}function $e(i,e,t){return Math.max(e,Math.min(t,i))}function fc(i,e){return(i%e+e)%e}function ks(i,e,t){return(1-t)*i+t*e}function hc(i){return Math.pow(2,Math.floor(Math.log(i)/Math.LN2))}function aa(i){switch(i.constructor){case Uint32Array:return 4294967295;case Uint16Array:return 65535;case Uint8Array:case Uint8ClampedArray:return 255;case Int32Array:return 2147483647;case Int16Array:return 32767;case Int8Array:return 127;default:return}}function pc(i,e){if(e.constructor===Float32Array)return i;const t=aa(e);if(t===void 0)throw new Error("THREE.MathUtils: Invalid component type.");return Math.max(i/t,-1)}function mc(i,e){if(e.constructor===Float32Array)return i;const t=aa(e);if(t===void 0)throw new Error("THREE.MathUtils: Invalid component type.");return Math.round(i*t)}const pa=class pa{constructor(e=0,t=0){this.x=e,this.y=t}get width(){return this.x}set width(e){this.x=e}get height(){return this.y}set height(e){this.y=e}set(e,t){return this.x=e,this.y=t,this}setScalar(e){return this.x=e,this.y=e,this}setX(e){return this.x=e,this}setY(e){return this.y=e,this}setComponent(e,t){switch(e){case 0:this.x=t;break;case 1:this.y=t;break;default:throw new Error("THREE.Vector2: index is out of range: "+e)}return this}getComponent(e){switch(e){case 0:return this.x;case 1:return this.y;default:throw new Error("THREE.Vector2: index is out of range: "+e)}}clone(){return new this.constructor(this.x,this.y)}copy(e){return this.x=e.x,this.y=e.y,this}add(e){return this.x+=e.x,this.y+=e.y,this}addScalar(e){return this.x+=e,this.y+=e,this}addVectors(e,t){return this.x=e.x+t.x,this.y=e.y+t.y,this}addScaledVector(e,t){return this.x+=e.x*t,this.y+=e.y*t,this}sub(e){return this.x-=e.x,this.y-=e.y,this}subScalar(e){return this.x-=e,this.y-=e,this}subVectors(e,t){return this.x=e.x-t.x,this.y=e.y-t.y,this}multiply(e){return this.x*=e.x,this.y*=e.y,this}multiplyScalar(e){return this.x*=e,this.y*=e,this}divide(e){return this.x/=e.x,this.y/=e.y,this}divideScalar(e){return this.multiplyScalar(1/e)}applyMatrix3(e){const t=this.x,n=this.y,s=e.elements;return this.x=s[0]*t+s[3]*n+s[6],this.y=s[1]*t+s[4]*n+s[7],this}min(e){return this.x=Math.min(this.x,e.x),this.y=Math.min(this.y,e.y),this}max(e){return this.x=Math.max(this.x,e.x),this.y=Math.max(this.y,e.y),this}clamp(e,t){return this.x=$e(this.x,e.x,t.x),this.y=$e(this.y,e.y,t.y),this}clampScalar(e,t){return this.x=$e(this.x,e,t),this.y=$e(this.y,e,t),this}clampLength(e,t){const n=this.length();return this.divideScalar(n||1).multiplyScalar($e(n,e,t))}floor(){return this.x=Math.floor(this.x),this.y=Math.floor(this.y),this}ceil(){return this.x=Math.ceil(this.x),this.y=Math.ceil(this.y),this}round(){return this.x=Math.round(this.x),this.y=Math.round(this.y),this}roundToZero(){return this.x=Math.trunc(this.x),this.y=Math.trunc(this.y),this}negate(){return this.x=-this.x,this.y=-this.y,this}dot(e){return this.x*e.x+this.y*e.y}cross(e){return this.x*e.y-this.y*e.x}lengthSq(){return this.x*this.x+this.y*this.y}length(){return Math.sqrt(this.x*this.x+this.y*this.y)}manhattanLength(){return Math.abs(this.x)+Math.abs(this.y)}normalize(){return this.divideScalar(this.length()||1)}angle(){return Math.atan2(-this.y,-this.x)+Math.PI}angleTo(e){const t=Math.sqrt(this.lengthSq()*e.lengthSq());if(t===0)return Math.PI/2;const n=this.dot(e)/t;return Math.acos($e(n,-1,1))}distanceTo(e){return Math.sqrt(this.distanceToSquared(e))}distanceToSquared(e){const t=this.x-e.x,n=this.y-e.y;return t*t+n*n}manhattanDistanceTo(e){return Math.abs(this.x-e.x)+Math.abs(this.y-e.y)}setLength(e){return this.normalize().multiplyScalar(e)}lerp(e,t){return this.x+=(e.x-this.x)*t,this.y+=(e.y-this.y)*t,this}lerpVectors(e,t,n){return this.x=e.x+(t.x-e.x)*n,this.y=e.y+(t.y-e.y)*n,this}equals(e){return e.x===this.x&&e.y===this.y}fromArray(e,t=0){return this.x=e[t],this.y=e[t+1],this}toArray(e=[],t=0){return e[t]=this.x,e[t+1]=this.y,e}fromBufferAttribute(e,t){return this.x=e.getX(t),this.y=e.getY(t),this}rotateAround(e,t){const n=Math.cos(t),s=Math.sin(t),r=this.x-e.x,a=this.y-e.y;return this.x=r*n-a*s+e.x,this.y=r*s+a*n+e.y,this}random(){return this.x=Math.random(),this.y=Math.random(),this}*[Symbol.iterator](){yield this.x,yield this.y}};pa.prototype.isVector2=!0;let Xe=pa;class Mi{constructor(e=0,t=0,n=0,s=1){this.isQuaternion=!0,this._x=e,this._y=t,this._z=n,this._w=s}static slerpFlat(e,t,n,s,r,a,o){let d=n[s+0],c=n[s+1],m=n[s+2],x=n[s+3],f=r[a+0],g=r[a+1],l=r[a+2],T=r[a+3];if(x!==T||d!==f||c!==g||m!==l){let p=d*f+c*g+m*l+x*T;p<0&&(f=-f,g=-g,l=-l,T=-T,p=-p);let u=1-o;if(p<.9995){const b=Math.acos(p),y=Math.sin(b);u=Math.sin(u*b)/y,o=Math.sin(o*b)/y,d=d*u+f*o,c=c*u+g*o,m=m*u+l*o,x=x*u+T*o}else{d=d*u+f*o,c=c*u+g*o,m=m*u+l*o,x=x*u+T*o;const b=1/Math.sqrt(d*d+c*c+m*m+x*x);d*=b,c*=b,m*=b,x*=b}}e[t]=d,e[t+1]=c,e[t+2]=m,e[t+3]=x}static multiplyQuaternionsFlat(e,t,n,s,r,a){const o=n[s],d=n[s+1],c=n[s+2],m=n[s+3],x=r[a],f=r[a+1],g=r[a+2],l=r[a+3];return e[t]=o*l+m*x+d*g-c*f,e[t+1]=d*l+m*f+c*x-o*g,e[t+2]=c*l+m*g+o*f-d*x,e[t+3]=m*l-o*x-d*f-c*g,e}get x(){return this._x}set x(e){this._x=e,this._onChangeCallback()}get y(){return this._y}set y(e){this._y=e,this._onChangeCallback()}get z(){return this._z}set z(e){this._z=e,this._onChangeCallback()}get w(){return this._w}set w(e){this._w=e,this._onChangeCallback()}set(e,t,n,s){return this._x=e,this._y=t,this._z=n,this._w=s,this._onChangeCallback(),this}clone(){return new this.constructor(this._x,this._y,this._z,this._w)}copy(e){return this._x=e.x,this._y=e.y,this._z=e.z,this._w=e.w,this._onChangeCallback(),this}setFromEuler(e,t=!0){const n=e._x,s=e._y,r=e._z,a=e._order,o=Math.cos,d=Math.sin,c=o(n/2),m=o(s/2),x=o(r/2),f=d(n/2),g=d(s/2),l=d(r/2);switch(a){case"XYZ":this._x=f*m*x+c*g*l,this._y=c*g*x-f*m*l,this._z=c*m*l+f*g*x,this._w=c*m*x-f*g*l;break;case"YXZ":this._x=f*m*x+c*g*l,this._y=c*g*x-f*m*l,this._z=c*m*l-f*g*x,this._w=c*m*x+f*g*l;break;case"ZXY":this._x=f*m*x-c*g*l,this._y=c*g*x+f*m*l,this._z=c*m*l+f*g*x,this._w=c*m*x-f*g*l;break;case"ZYX":this._x=f*m*x-c*g*l,this._y=c*g*x+f*m*l,this._z=c*m*l-f*g*x,this._w=c*m*x+f*g*l;break;case"YZX":this._x=f*m*x+c*g*l,this._y=c*g*x+f*m*l,this._z=c*m*l-f*g*x,this._w=c*m*x-f*g*l;break;case"XZY":this._x=f*m*x-c*g*l,this._y=c*g*x-f*m*l,this._z=c*m*l+f*g*x,this._w=c*m*x+f*g*l;break;default:De("Quaternion: .setFromEuler() encountered an unknown order: "+a)}return t===!0&&this._onChangeCallback(),this}setFromAxisAngle(e,t){const n=t/2,s=Math.sin(n);return this._x=e.x*s,this._y=e.y*s,this._z=e.z*s,this._w=Math.cos(n),this._onChangeCallback(),this}setFromRotationMatrix(e){const t=e.elements,n=t[0],s=t[4],r=t[8],a=t[1],o=t[5],d=t[9],c=t[2],m=t[6],x=t[10],f=n+o+x;if(f>0){const g=.5/Math.sqrt(f+1);this._w=.25/g,this._x=(m-d)*g,this._y=(r-c)*g,this._z=(a-s)*g}else if(n>o&&n>x){const g=2*Math.sqrt(1+n-o-x);this._w=(m-d)/g,this._x=.25*g,this._y=(s+a)/g,this._z=(r+c)/g}else if(o>x){const g=2*Math.sqrt(1+o-n-x);this._w=(r-c)/g,this._x=(s+a)/g,this._y=.25*g,this._z=(d+m)/g}else{const g=2*Math.sqrt(1+x-n-o);this._w=(a-s)/g,this._x=(r+c)/g,this._y=(d+m)/g,this._z=.25*g}return this._onChangeCallback(),this}setFromUnitVectors(e,t){let n=e.dot(t)+1;return n<1e-8?(n=0,Math.abs(e.x)>Math.abs(e.z)?(this._x=-e.y,this._y=e.x,this._z=0,this._w=n):(this._x=0,this._y=-e.z,this._z=e.y,this._w=n)):(this._x=e.y*t.z-e.z*t.y,this._y=e.z*t.x-e.x*t.z,this._z=e.x*t.y-e.y*t.x,this._w=n),this.normalize()}angleTo(e){return 2*Math.acos(Math.abs($e(this.dot(e),-1,1)))}rotateTowards(e,t){const n=this.angleTo(e);if(n===0)return this;const s=Math.min(1,t/n);return this.slerp(e,s),this}identity(){return this.set(0,0,0,1)}invert(){return this.conjugate()}conjugate(){return this._x*=-1,this._y*=-1,this._z*=-1,this._onChangeCallback(),this}dot(e){return this._x*e._x+this._y*e._y+this._z*e._z+this._w*e._w}lengthSq(){return this._x*this._x+this._y*this._y+this._z*this._z+this._w*this._w}length(){return Math.sqrt(this._x*this._x+this._y*this._y+this._z*this._z+this._w*this._w)}normalize(){let e=this.length();return e===0?(this._x=0,this._y=0,this._z=0,this._w=1):(e=1/e,this._x=this._x*e,this._y=this._y*e,this._z=this._z*e,this._w=this._w*e),this._onChangeCallback(),this}multiply(e){return this.multiplyQuaternions(this,e)}premultiply(e){return this.multiplyQuaternions(e,this)}multiplyQuaternions(e,t){const n=e._x,s=e._y,r=e._z,a=e._w,o=t._x,d=t._y,c=t._z,m=t._w;return this._x=n*m+a*o+s*c-r*d,this._y=s*m+a*d+r*o-n*c,this._z=r*m+a*c+n*d-s*o,this._w=a*m-n*o-s*d-r*c,this._onChangeCallback(),this}slerp(e,t){let n=e._x,s=e._y,r=e._z,a=e._w,o=this.dot(e);o<0&&(n=-n,s=-s,r=-r,a=-a,o=-o);let d=1-t;if(o<.9995){const c=Math.acos(o),m=Math.sin(c);d=Math.sin(d*c)/m,t=Math.sin(t*c)/m,this._x=this._x*d+n*t,this._y=this._y*d+s*t,this._z=this._z*d+r*t,this._w=this._w*d+a*t,this._onChangeCallback()}else this._x=this._x*d+n*t,this._y=this._y*d+s*t,this._z=this._z*d+r*t,this._w=this._w*d+a*t,this.normalize();return this}slerpQuaternions(e,t,n){return this.copy(e).slerp(t,n)}random(){const e=2*Math.PI*Math.random(),t=2*Math.PI*Math.random(),n=Math.random(),s=Math.sqrt(1-n),r=Math.sqrt(n);return this.set(s*Math.sin(e),s*Math.cos(e),r*Math.sin(t),r*Math.cos(t))}equals(e){return e._x===this._x&&e._y===this._y&&e._z===this._z&&e._w===this._w}fromArray(e,t=0){return this._x=e[t],this._y=e[t+1],this._z=e[t+2],this._w=e[t+3],this._onChangeCallback(),this}toArray(e=[],t=0){return e[t]=this._x,e[t+1]=this._y,e[t+2]=this._z,e[t+3]=this._w,e}fromBufferAttribute(e,t){return this._x=e.getX(t),this._y=e.getY(t),this._z=e.getZ(t),this._w=e.getW(t),this._onChangeCallback(),this}toJSON(){return this.toArray()}_onChange(e){return this._onChangeCallback=e,this}_onChangeCallback(){}*[Symbol.iterator](){yield this._x,yield this._y,yield this._z,yield this._w}}const ma=class ma{constructor(e=0,t=0,n=0){this.x=e,this.y=t,this.z=n}set(e,t,n){return n===void 0&&(n=this.z),this.x=e,this.y=t,this.z=n,this}setScalar(e){return this.x=e,this.y=e,this.z=e,this}setX(e){return this.x=e,this}setY(e){return this.y=e,this}setZ(e){return this.z=e,this}setComponent(e,t){switch(e){case 0:this.x=t;break;case 1:this.y=t;break;case 2:this.z=t;break;default:throw new Error("THREE.Vector3: index is out of range: "+e)}return this}getComponent(e){switch(e){case 0:return this.x;case 1:return this.y;case 2:return this.z;default:throw new Error("THREE.Vector3: index is out of range: "+e)}}clone(){return new this.constructor(this.x,this.y,this.z)}copy(e){return this.x=e.x,this.y=e.y,this.z=e.z,this}add(e){return this.x+=e.x,this.y+=e.y,this.z+=e.z,this}addScalar(e){return this.x+=e,this.y+=e,this.z+=e,this}addVectors(e,t){return this.x=e.x+t.x,this.y=e.y+t.y,this.z=e.z+t.z,this}addScaledVector(e,t){return this.x+=e.x*t,this.y+=e.y*t,this.z+=e.z*t,this}sub(e){return this.x-=e.x,this.y-=e.y,this.z-=e.z,this}subScalar(e){return this.x-=e,this.y-=e,this.z-=e,this}subVectors(e,t){return this.x=e.x-t.x,this.y=e.y-t.y,this.z=e.z-t.z,this}multiply(e){return this.x*=e.x,this.y*=e.y,this.z*=e.z,this}multiplyScalar(e){return this.x*=e,this.y*=e,this.z*=e,this}multiplyVectors(e,t){return this.x=e.x*t.x,this.y=e.y*t.y,this.z=e.z*t.z,this}applyEuler(e){return this.applyQuaternion(Oa.setFromEuler(e))}applyAxisAngle(e,t){return this.applyQuaternion(Oa.setFromAxisAngle(e,t))}applyMatrix3(e){const t=this.x,n=this.y,s=this.z,r=e.elements;return this.x=r[0]*t+r[3]*n+r[6]*s,this.y=r[1]*t+r[4]*n+r[7]*s,this.z=r[2]*t+r[5]*n+r[8]*s,this}applyNormalMatrix(e){return this.applyMatrix3(e).normalize()}applyMatrix4(e){const t=this.x,n=this.y,s=this.z,r=e.elements,a=1/(r[3]*t+r[7]*n+r[11]*s+r[15]);return this.x=(r[0]*t+r[4]*n+r[8]*s+r[12])*a,this.y=(r[1]*t+r[5]*n+r[9]*s+r[13])*a,this.z=(r[2]*t+r[6]*n+r[10]*s+r[14])*a,this}applyQuaternion(e){const t=this.x,n=this.y,s=this.z,r=e.x,a=e.y,o=e.z,d=e.w,c=2*(a*s-o*n),m=2*(o*t-r*s),x=2*(r*n-a*t);return this.x=t+d*c+a*x-o*m,this.y=n+d*m+o*c-r*x,this.z=s+d*x+r*m-a*c,this}project(e){return this.applyMatrix4(e.matrixWorldInverse).applyMatrix4(e.projectionMatrix)}unproject(e){return this.applyMatrix4(e.projectionMatrixInverse).applyMatrix4(e.matrixWorld)}transformDirection(e){const t=this.x,n=this.y,s=this.z,r=e.elements;return this.x=r[0]*t+r[4]*n+r[8]*s,this.y=r[1]*t+r[5]*n+r[9]*s,this.z=r[2]*t+r[6]*n+r[10]*s,this.normalize()}divide(e){return this.x/=e.x,this.y/=e.y,this.z/=e.z,this}divideScalar(e){return this.multiplyScalar(1/e)}min(e){return this.x=Math.min(this.x,e.x),this.y=Math.min(this.y,e.y),this.z=Math.min(this.z,e.z),this}max(e){return this.x=Math.max(this.x,e.x),this.y=Math.max(this.y,e.y),this.z=Math.max(this.z,e.z),this}clamp(e,t){return this.x=$e(this.x,e.x,t.x),this.y=$e(this.y,e.y,t.y),this.z=$e(this.z,e.z,t.z),this}clampScalar(e,t){return this.x=$e(this.x,e,t),this.y=$e(this.y,e,t),this.z=$e(this.z,e,t),this}clampLength(e,t){const n=this.length();return this.divideScalar(n||1).multiplyScalar($e(n,e,t))}floor(){return this.x=Math.floor(this.x),this.y=Math.floor(this.y),this.z=Math.floor(this.z),this}ceil(){return this.x=Math.ceil(this.x),this.y=Math.ceil(this.y),this.z=Math.ceil(this.z),this}round(){return this.x=Math.round(this.x),this.y=Math.round(this.y),this.z=Math.round(this.z),this}roundToZero(){return this.x=Math.trunc(this.x),this.y=Math.trunc(this.y),this.z=Math.trunc(this.z),this}negate(){return this.x=-this.x,this.y=-this.y,this.z=-this.z,this}dot(e){return this.x*e.x+this.y*e.y+this.z*e.z}lengthSq(){return this.x*this.x+this.y*this.y+this.z*this.z}length(){return Math.sqrt(this.x*this.x+this.y*this.y+this.z*this.z)}manhattanLength(){return Math.abs(this.x)+Math.abs(this.y)+Math.abs(this.z)}normalize(){return this.divideScalar(this.length()||1)}setLength(e){return this.normalize().multiplyScalar(e)}lerp(e,t){return this.x+=(e.x-this.x)*t,this.y+=(e.y-this.y)*t,this.z+=(e.z-this.z)*t,this}lerpVectors(e,t,n){return this.x=e.x+(t.x-e.x)*n,this.y=e.y+(t.y-e.y)*n,this.z=e.z+(t.z-e.z)*n,this}cross(e){return this.crossVectors(this,e)}crossVectors(e,t){const n=e.x,s=e.y,r=e.z,a=t.x,o=t.y,d=t.z;return this.x=s*d-r*o,this.y=r*a-n*d,this.z=n*o-s*a,this}projectOnVector(e){const t=e.lengthSq();if(t===0)return this.set(0,0,0);const n=e.dot(this)/t;return this.copy(e).multiplyScalar(n)}projectOnPlane(e){return zs.copy(this).projectOnVector(e),this.sub(zs)}reflect(e){return this.sub(zs.copy(e).multiplyScalar(2*this.dot(e)))}angleTo(e){const t=Math.sqrt(this.lengthSq()*e.lengthSq());if(t===0)return Math.PI/2;const n=this.dot(e)/t;return Math.acos($e(n,-1,1))}distanceTo(e){return Math.sqrt(this.distanceToSquared(e))}distanceToSquared(e){const t=this.x-e.x,n=this.y-e.y,s=this.z-e.z;return t*t+n*n+s*s}manhattanDistanceTo(e){return Math.abs(this.x-e.x)+Math.abs(this.y-e.y)+Math.abs(this.z-e.z)}setFromSpherical(e){return this.setFromSphericalCoords(e.radius,e.phi,e.theta)}setFromSphericalCoords(e,t,n){const s=Math.sin(t)*e;return this.x=s*Math.sin(n),this.y=Math.cos(t)*e,this.z=s*Math.cos(n),this}setFromCylindrical(e){return this.setFromCylindricalCoords(e.radius,e.theta,e.y)}setFromCylindricalCoords(e,t,n){return this.x=e*Math.sin(t),this.y=n,this.z=e*Math.cos(t),this}setFromMatrixPosition(e){const t=e.elements;return this.x=t[12],this.y=t[13],this.z=t[14],this}setFromMatrixScale(e){const t=this.setFromMatrixColumn(e,0).length(),n=this.setFromMatrixColumn(e,1).length(),s=this.setFromMatrixColumn(e,2).length();return this.x=t,this.y=n,this.z=s,this}setFromMatrixColumn(e,t){return this.fromArray(e.elements,t*4)}setFromMatrix3Column(e,t){return this.fromArray(e.elements,t*3)}setFromEuler(e){return this.x=e._x,this.y=e._y,this.z=e._z,this}setFromColor(e){return this.x=e.r,this.y=e.g,this.z=e.b,this}equals(e){return e.x===this.x&&e.y===this.y&&e.z===this.z}fromArray(e,t=0){return this.x=e[t],this.y=e[t+1],this.z=e[t+2],this}toArray(e=[],t=0){return e[t]=this.x,e[t+1]=this.y,e[t+2]=this.z,e}fromBufferAttribute(e,t){return this.x=e.getX(t),this.y=e.getY(t),this.z=e.getZ(t),this}random(){return this.x=Math.random(),this.y=Math.random(),this.z=Math.random(),this}randomDirection(){const e=Math.random()*Math.PI*2,t=Math.random()*2-1,n=Math.sqrt(1-t*t);return this.x=n*Math.cos(e),this.y=t,this.z=n*Math.sin(e),this}*[Symbol.iterator](){yield this.x,yield this.y,yield this.z}};ma.prototype.isVector3=!0;let z=ma;const zs=new z,Oa=new Mi,ga=class ga{constructor(e,t,n,s,r,a,o,d,c){this.elements=[1,0,0,0,1,0,0,0,1],e!==void 0&&this.set(e,t,n,s,r,a,o,d,c)}set(e,t,n,s,r,a,o,d,c){const m=this.elements;return m[0]=e,m[1]=s,m[2]=o,m[3]=t,m[4]=r,m[5]=d,m[6]=n,m[7]=a,m[8]=c,this}identity(){return this.set(1,0,0,0,1,0,0,0,1),this}copy(e){const t=this.elements,n=e.elements;return t[0]=n[0],t[1]=n[1],t[2]=n[2],t[3]=n[3],t[4]=n[4],t[5]=n[5],t[6]=n[6],t[7]=n[7],t[8]=n[8],this}extractBasis(e,t,n){return e.setFromMatrix3Column(this,0),t.setFromMatrix3Column(this,1),n.setFromMatrix3Column(this,2),this}setFromMatrix4(e){const t=e.elements;return this.set(t[0],t[4],t[8],t[1],t[5],t[9],t[2],t[6],t[10]),this}multiply(e){return this.multiplyMatrices(this,e)}premultiply(e){return this.multiplyMatrices(e,this)}multiplyMatrices(e,t){const n=e.elements,s=t.elements,r=this.elements,a=n[0],o=n[3],d=n[6],c=n[1],m=n[4],x=n[7],f=n[2],g=n[5],l=n[8],T=s[0],p=s[3],u=s[6],b=s[1],y=s[4],E=s[7],R=s[2],A=s[5],h=s[8];return r[0]=a*T+o*b+d*R,r[3]=a*p+o*y+d*A,r[6]=a*u+o*E+d*h,r[1]=c*T+m*b+x*R,r[4]=c*p+m*y+x*A,r[7]=c*u+m*E+x*h,r[2]=f*T+g*b+l*R,r[5]=f*p+g*y+l*A,r[8]=f*u+g*E+l*h,this}multiplyScalar(e){const t=this.elements;return t[0]*=e,t[3]*=e,t[6]*=e,t[1]*=e,t[4]*=e,t[7]*=e,t[2]*=e,t[5]*=e,t[8]*=e,this}determinant(){const e=this.elements,t=e[0],n=e[1],s=e[2],r=e[3],a=e[4],o=e[5],d=e[6],c=e[7],m=e[8];return t*a*m-t*o*c-n*r*m+n*o*d+s*r*c-s*a*d}invert(){const e=this.elements,t=e[0],n=e[1],s=e[2],r=e[3],a=e[4],o=e[5],d=e[6],c=e[7],m=e[8],x=m*a-o*c,f=o*d-m*r,g=c*r-a*d,l=t*x+n*f+s*g;if(l===0)return this.set(0,0,0,0,0,0,0,0,0);const T=1/l;return e[0]=x*T,e[1]=(s*c-m*n)*T,e[2]=(o*n-s*a)*T,e[3]=f*T,e[4]=(m*t-s*d)*T,e[5]=(s*r-o*t)*T,e[6]=g*T,e[7]=(n*d-c*t)*T,e[8]=(a*t-n*r)*T,this}transpose(){let e;const t=this.elements;return e=t[1],t[1]=t[3],t[3]=e,e=t[2],t[2]=t[6],t[6]=e,e=t[5],t[5]=t[7],t[7]=e,this}getNormalMatrix(e){return this.setFromMatrix4(e).invert().transpose()}transposeIntoArray(e){const t=this.elements;return e[0]=t[0],e[1]=t[3],e[2]=t[6],e[3]=t[1],e[4]=t[4],e[5]=t[7],e[6]=t[2],e[7]=t[5],e[8]=t[8],this}setUvTransform(e,t,n,s,r,a,o){const d=Math.cos(r),c=Math.sin(r);return this.set(n*d,n*c,-n*(d*a+c*o)+a+e,-s*c,s*d,-s*(-c*a+d*o)+o+t,0,0,1),this}scale(e,t){return xs("Matrix3: .scale() is deprecated. Use .makeScale() instead."),this.premultiply(Gs.makeScale(e,t)),this}rotate(e){return xs("Matrix3: .rotate() is deprecated. Use .makeRotation() instead."),this.premultiply(Gs.makeRotation(-e)),this}translate(e,t){return xs("Matrix3: .translate() is deprecated. Use .makeTranslation() instead."),this.premultiply(Gs.makeTranslation(e,t)),this}makeTranslation(e,t){return e.isVector2?this.set(1,0,e.x,0,1,e.y,0,0,1):this.set(1,0,e,0,1,t,0,0,1),this}makeRotation(e){const t=Math.cos(e),n=Math.sin(e);return this.set(t,-n,0,n,t,0,0,0,1),this}makeScale(e,t){return this.set(e,0,0,0,t,0,0,0,1),this}equals(e){const t=this.elements,n=e.elements;for(let s=0;s<9;s++)if(t[s]!==n[s])return!1;return!0}fromArray(e,t=0){for(let n=0;n<9;n++)this.elements[n]=e[n+t];return this}toArray(e=[],t=0){const n=this.elements;return e[t]=n[0],e[t+1]=n[1],e[t+2]=n[2],e[t+3]=n[3],e[t+4]=n[4],e[t+5]=n[5],e[t+6]=n[6],e[t+7]=n[7],e[t+8]=n[8],e}clone(){return new this.constructor().fromArray(this.elements)}};ga.prototype.isMatrix3=!0;let Ne=ga;const Gs=new Ne,Ba=new Ne().set(.4123908,.3575843,.1804808,.212639,.7151687,.0721923,.0193308,.1191948,.9505322),ka=new Ne().set(3.2409699,-1.5373832,-.4986108,-.9692436,1.8759675,.0415551,.0556301,-.203977,1.0569715);function gc(){const i={enabled:!0,workingColorSpace:Ms,spaces:{},convert:function(s,r,a){return this.enabled===!1||r===a||!r||!a||(this.spaces[r].transfer===at&&(s.r=An(s.r),s.g=An(s.g),s.b=An(s.b)),this.spaces[r].primaries!==this.spaces[a].primaries&&(s.applyMatrix3(this.spaces[r].toXYZ),s.applyMatrix3(this.spaces[a].fromXYZ)),this.spaces[a].transfer===at&&(s.r=gi(s.r),s.g=gi(s.g),s.b=gi(s.b))),s},workingToColorSpace:function(s,r){return this.convert(s,this.workingColorSpace,r)},colorSpaceToWorking:function(s,r){return this.convert(s,r,this.workingColorSpace)},getPrimaries:function(s){return this.spaces[s].primaries},getTransfer:function(s){return s===Fn?bs:this.spaces[s].transfer},getToneMappingMode:function(s){return this.spaces[s].outputColorSpaceConfig.toneMappingMode||"standard"},getLuminanceCoefficients:function(s,r=this.workingColorSpace){return s.fromArray(this.spaces[r].luminanceCoefficients)},define:function(s){Object.assign(this.spaces,s)},_getMatrix:function(s,r,a){return s.copy(this.spaces[a].fromXYZ).multiply(this.spaces[r].toXYZ)},_getDrawingBufferColorSpace:function(s){return this.spaces[s].outputColorSpaceConfig.drawingBufferColorSpace},_getUnpackColorSpace:function(s=this.workingColorSpace){return this.spaces[s].workingColorSpaceConfig.unpackColorSpace}},e=[.64,.33,.3,.6,.15,.06],t=[.2126,.7152,.0722],n=[.3127,.329];return i.define({[Ms]:{primaries:e,whitePoint:n,transfer:bs,toXYZ:Ba,fromXYZ:ka,luminanceCoefficients:t,workingColorSpaceConfig:{unpackColorSpace:Jt},outputColorSpaceConfig:{drawingBufferColorSpace:Jt}},[Jt]:{primaries:e,whitePoint:n,transfer:at,toXYZ:Ba,fromXYZ:ka,luminanceCoefficients:t,outputColorSpaceConfig:{drawingBufferColorSpace:Jt}}}),i}const Ze=gc();function An(i){return i<.04045?i*.0773993808:Math.pow(i*.9478672986+.0521327014,2.4)}function gi(i){return i<.0031308?i*12.92:1.055*Math.pow(i,.41666)-.055}let ni;class xc{static getDataURL(e,t="image/png"){if(/^data:/i.test(e.src)||typeof HTMLCanvasElement>"u")return e.src;let n;if(e instanceof HTMLCanvasElement)n=e;else{ni===void 0&&(ni=ys("canvas")),ni.width=e.width,ni.height=e.height;const s=ni.getContext("2d");e instanceof ImageData?s.putImageData(e,0,0):s.drawImage(e,0,0,e.width,e.height),n=ni}return n.toDataURL(t)}static sRGBToLinear(e){if(typeof HTMLImageElement<"u"&&e instanceof HTMLImageElement||typeof HTMLCanvasElement<"u"&&e instanceof HTMLCanvasElement||typeof ImageBitmap<"u"&&e instanceof ImageBitmap){const t=ys("canvas");t.width=e.width,t.height=e.height;const n=t.getContext("2d");n.drawImage(e,0,0,e.width,e.height);const s=n.getImageData(0,0,e.width,e.height),r=s.data;for(let a=0;a<r.length;a++)r[a]=An(r[a]/255)*255;return n.putImageData(s,0,0),t}else if(e.data){const t=e.data.slice(0);for(let n=0;n<t.length;n++)t instanceof Uint8Array||t instanceof Uint8ClampedArray?t[n]=Math.floor(An(t[n]/255)*255):t[n]=An(t[n]);return{data:t,width:e.width,height:e.height}}else return De("ImageUtils.sRGBToLinear(): Unsupported image type. No color space conversion applied."),e}}let _c=0;class oa{constructor(e=null){this.isTextureSource=!0,Object.defineProperty(this,"id",{value:_c++}),this.uuid=Oi(),this.data=e,this.dataReady=!0,this.version=0}getSize(e){const t=this.data;return typeof HTMLVideoElement<"u"&&t instanceof HTMLVideoElement?e.set(t.videoWidth,t.videoHeight,0):typeof VideoFrame<"u"&&t instanceof VideoFrame?e.set(t.displayWidth,t.displayHeight,0):t!==null?e.set(t.width||0,t.height||0,t.depth||0):e.set(0,0,0),e}set needsUpdate(e){e===!0&&this.version++}toJSON(e){const t=e===void 0||typeof e=="string";if(!t&&e.images[this.uuid]!==void 0)return e.images[this.uuid];const n={uuid:this.uuid,url:""},s=this.data;if(s!==null){let r;if(Array.isArray(s)){r=[];for(let a=0,o=s.length;a<o;a++)s[a].isDataTexture?r.push(Hs(s[a].image)):r.push(Hs(s[a]))}else r=Hs(s);n.url=r}return t||(e.images[this.uuid]=n),n}}function Hs(i){return typeof HTMLImageElement<"u"&&i instanceof HTMLImageElement||typeof HTMLCanvasElement<"u"&&i instanceof HTMLCanvasElement||typeof ImageBitmap<"u"&&i instanceof ImageBitmap?xc.getDataURL(i):i.data?{data:Array.from(i.data),width:i.width,height:i.height,type:i.data.constructor.name}:(De("Texture: Unable to serialize Texture."),{})}let vc=0;const Vs=new z;class zt extends ei{constructor(e=zt.DEFAULT_IMAGE,t=zt.DEFAULT_MAPPING,n=Tn,s=Tn,r=Wt,a=On,o=pn,d=Zt,c=zt.DEFAULT_ANISOTROPY,m=Fn){super(),this.isTexture=!0,Object.defineProperty(this,"id",{value:vc++}),this.uuid=Oi(),this.name="",this.source=new oa(e),this.mipmaps=[],this.mapping=t,this.channel=0,this.wrapS=n,this.wrapT=s,this.magFilter=r,this.minFilter=a,this.anisotropy=c,this.format=o,this.internalFormat=null,this.type=d,this.offset=new Xe(0,0),this.repeat=new Xe(1,1),this.center=new Xe(0,0),this.rotation=0,this.matrixAutoUpdate=!0,this.matrix=new Ne,this.generateMipmaps=!0,this.mipmapsAutoUpdate=!0,this.premultiplyAlpha=!1,this.flipY=!0,this.unpackAlignment=4,this.colorSpace=m,this.userData={},this.updateRanges=[],this.version=0,this.onUpdate=null,this.renderTarget=null,this.isRenderTargetTexture=!1,this.isArrayTexture=!!(e&&e.depth&&e.depth>1),this.pmremVersion=0,this.isPMREMTexture=!1,this.normalized=!1}get width(){return this.source.getSize(Vs).x}get height(){return this.source.getSize(Vs).y}get depth(){return this.source.getSize(Vs).z}get image(){return this.source.data}set image(e){this.source.data=e}updateMatrix(){this.matrix.setUvTransform(this.offset.x,this.offset.y,this.repeat.x,this.repeat.y,this.rotation,this.center.x,this.center.y)}addUpdateRange(e,t){this.updateRanges.push({start:e,count:t})}clearUpdateRanges(){this.updateRanges.length=0}clone(){return new this.constructor().copy(this)}copy(e){return this.name=e.name,this.source=e.source,this.mipmaps=e.mipmaps.slice(0),this.mapping=e.mapping,this.channel=e.channel,this.wrapS=e.wrapS,this.wrapT=e.wrapT,this.magFilter=e.magFilter,this.minFilter=e.minFilter,this.anisotropy=e.anisotropy,this.format=e.format,this.internalFormat=e.internalFormat,this.type=e.type,this.normalized=e.normalized,this.offset.copy(e.offset),this.repeat.copy(e.repeat),this.center.copy(e.center),this.rotation=e.rotation,this.matrixAutoUpdate=e.matrixAutoUpdate,this.matrix.copy(e.matrix),this.generateMipmaps=e.generateMipmaps,this.mipmapsAutoUpdate=e.mipmapsAutoUpdate,this.premultiplyAlpha=e.premultiplyAlpha,this.flipY=e.flipY,this.unpackAlignment=e.unpackAlignment,this.colorSpace=e.colorSpace,this.renderTarget=e.renderTarget,this.isRenderTargetTexture=e.isRenderTargetTexture,this.isPMREMTexture=e.isPMREMTexture,this.isArrayTexture=e.isArrayTexture,this.userData=JSON.parse(JSON.stringify(e.userData)),this.needsUpdate=!0,this}setValues(e){for(const t in e){const n=e[t];if(n===void 0){De(`Texture.setValues(): parameter '${t}' has value of undefined.`);continue}const s=this[t];if(s===void 0){De(`Texture.setValues(): property '${t}' does not exist.`);continue}s&&n&&s.isVector2&&n.isVector2||s&&n&&s.isVector3&&n.isVector3||s&&n&&s.isMatrix3&&n.isMatrix3?s.copy(n):this[t]=n}}toJSON(e){const t=e===void 0||typeof e=="string";if(!t&&e.textures[this.uuid]!==void 0)return e.textures[this.uuid];const n={metadata:{version:4.7,type:"Texture",generator:"Texture.toJSON"},uuid:this.uuid,name:this.name,image:this.source.toJSON(e).uuid,mapping:this.mapping,channel:this.channel,repeat:[this.repeat.x,this.repeat.y],offset:[this.offset.x,this.offset.y],center:[this.center.x,this.center.y],rotation:this.rotation,wrap:[this.wrapS,this.wrapT],format:this.format,internalFormat:this.internalFormat,type:this.type,normalized:this.normalized,colorSpace:this.colorSpace,minFilter:this.minFilter,magFilter:this.magFilter,anisotropy:this.anisotropy,flipY:this.flipY,generateMipmaps:this.generateMipmaps,mipmapsAutoUpdate:this.mipmapsAutoUpdate,premultiplyAlpha:this.premultiplyAlpha,unpackAlignment:this.unpackAlignment};return Object.keys(this.userData).length>0&&(n.userData=this.userData),t||(e.textures[this.uuid]=n),n}dispose(){this.dispatchEvent({type:"dispose"})}transformUv(e){if(this.mapping!==Bo)return e;if(e.applyMatrix3(this.matrix),e.x<0||e.x>1)switch(this.wrapS){case gr:e.x=e.x-Math.floor(e.x);break;case Tn:e.x=e.x<0?0:1;break;case xr:Math.abs(Math.floor(e.x)%2)===1?e.x=Math.ceil(e.x)-e.x:e.x=e.x-Math.floor(e.x);break}if(e.y<0||e.y>1)switch(this.wrapT){case gr:e.y=e.y-Math.floor(e.y);break;case Tn:e.y=e.y<0?0:1;break;case xr:Math.abs(Math.floor(e.y)%2)===1?e.y=Math.ceil(e.y)-e.y:e.y=e.y-Math.floor(e.y);break}return this.flipY&&(e.y=1-e.y),e}set needsUpdate(e){e===!0&&(this.version++,this.source.needsUpdate=!0)}set needsPMREMUpdate(e){e===!0&&this.pmremVersion++}}zt.DEFAULT_IMAGE=null;zt.DEFAULT_MAPPING=Bo;zt.DEFAULT_ANISOTROPY=1;const xa=class xa{constructor(e=0,t=0,n=0,s=1){this.x=e,this.y=t,this.z=n,this.w=s}get width(){return this.z}set width(e){this.z=e}get height(){return this.w}set height(e){this.w=e}set(e,t,n,s){return this.x=e,this.y=t,this.z=n,this.w=s,this}setScalar(e){return this.x=e,this.y=e,this.z=e,this.w=e,this}setX(e){return this.x=e,this}setY(e){return this.y=e,this}setZ(e){return this.z=e,this}setW(e){return this.w=e,this}setComponent(e,t){switch(e){case 0:this.x=t;break;case 1:this.y=t;break;case 2:this.z=t;break;case 3:this.w=t;break;default:throw new Error("THREE.Vector4: index is out of range: "+e)}return this}getComponent(e){switch(e){case 0:return this.x;case 1:return this.y;case 2:return this.z;case 3:return this.w;default:throw new Error("THREE.Vector4: index is out of range: "+e)}}clone(){return new this.constructor(this.x,this.y,this.z,this.w)}copy(e){return this.x=e.x,this.y=e.y,this.z=e.z,this.w=e.w!==void 0?e.w:1,this}add(e){return this.x+=e.x,this.y+=e.y,this.z+=e.z,this.w+=e.w,this}addScalar(e){return this.x+=e,this.y+=e,this.z+=e,this.w+=e,this}addVectors(e,t){return this.x=e.x+t.x,this.y=e.y+t.y,this.z=e.z+t.z,this.w=e.w+t.w,this}addScaledVector(e,t){return this.x+=e.x*t,this.y+=e.y*t,this.z+=e.z*t,this.w+=e.w*t,this}sub(e){return this.x-=e.x,this.y-=e.y,this.z-=e.z,this.w-=e.w,this}subScalar(e){return this.x-=e,this.y-=e,this.z-=e,this.w-=e,this}subVectors(e,t){return this.x=e.x-t.x,this.y=e.y-t.y,this.z=e.z-t.z,this.w=e.w-t.w,this}multiply(e){return this.x*=e.x,this.y*=e.y,this.z*=e.z,this.w*=e.w,this}multiplyScalar(e){return this.x*=e,this.y*=e,this.z*=e,this.w*=e,this}applyMatrix4(e){const t=this.x,n=this.y,s=this.z,r=this.w,a=e.elements;return this.x=a[0]*t+a[4]*n+a[8]*s+a[12]*r,this.y=a[1]*t+a[5]*n+a[9]*s+a[13]*r,this.z=a[2]*t+a[6]*n+a[10]*s+a[14]*r,this.w=a[3]*t+a[7]*n+a[11]*s+a[15]*r,this}divide(e){return this.x/=e.x,this.y/=e.y,this.z/=e.z,this.w/=e.w,this}divideScalar(e){return this.multiplyScalar(1/e)}setAxisAngleFromQuaternion(e){this.w=2*Math.acos(e.w);const t=Math.sqrt(1-e.w*e.w);return t<1e-4?(this.x=1,this.y=0,this.z=0):(this.x=e.x/t,this.y=e.y/t,this.z=e.z/t),this}setAxisAngleFromRotationMatrix(e){let t,n,s,r;const d=e.elements,c=d[0],m=d[4],x=d[8],f=d[1],g=d[5],l=d[9],T=d[2],p=d[6],u=d[10];if(Math.abs(m-f)<.01&&Math.abs(x-T)<.01&&Math.abs(l-p)<.01){if(Math.abs(m+f)<.1&&Math.abs(x+T)<.1&&Math.abs(l+p)<.1&&Math.abs(c+g+u-3)<.1)return this.set(1,0,0,0),this;t=Math.PI;const y=(c+1)/2,E=(g+1)/2,R=(u+1)/2,A=(m+f)/4,h=(x+T)/4,M=(l+p)/4;return y>E&&y>R?y<.01?(n=0,s=.707106781,r=.707106781):(n=Math.sqrt(y),s=A/n,r=h/n):E>R?E<.01?(n=.707106781,s=0,r=.707106781):(s=Math.sqrt(E),n=A/s,r=M/s):R<.01?(n=.707106781,s=.707106781,r=0):(r=Math.sqrt(R),n=h/r,s=M/r),this.set(n,s,r,t),this}let b=Math.sqrt((p-l)*(p-l)+(x-T)*(x-T)+(f-m)*(f-m));return Math.abs(b)<.001&&(b=1),this.x=(p-l)/b,this.y=(x-T)/b,this.z=(f-m)/b,this.w=Math.acos((c+g+u-1)/2),this}setFromMatrixPosition(e){const t=e.elements;return this.x=t[12],this.y=t[13],this.z=t[14],this.w=t[15],this}min(e){return this.x=Math.min(this.x,e.x),this.y=Math.min(this.y,e.y),this.z=Math.min(this.z,e.z),this.w=Math.min(this.w,e.w),this}max(e){return this.x=Math.max(this.x,e.x),this.y=Math.max(this.y,e.y),this.z=Math.max(this.z,e.z),this.w=Math.max(this.w,e.w),this}clamp(e,t){return this.x=$e(this.x,e.x,t.x),this.y=$e(this.y,e.y,t.y),this.z=$e(this.z,e.z,t.z),this.w=$e(this.w,e.w,t.w),this}clampScalar(e,t){return this.x=$e(this.x,e,t),this.y=$e(this.y,e,t),this.z=$e(this.z,e,t),this.w=$e(this.w,e,t),this}clampLength(e,t){const n=this.length();return this.divideScalar(n||1).multiplyScalar($e(n,e,t))}floor(){return this.x=Math.floor(this.x),this.y=Math.floor(this.y),this.z=Math.floor(this.z),this.w=Math.floor(this.w),this}ceil(){return this.x=Math.ceil(this.x),this.y=Math.ceil(this.y),this.z=Math.ceil(this.z),this.w=Math.ceil(this.w),this}round(){return this.x=Math.round(this.x),this.y=Math.round(this.y),this.z=Math.round(this.z),this.w=Math.round(this.w),this}roundToZero(){return this.x=Math.trunc(this.x),this.y=Math.trunc(this.y),this.z=Math.trunc(this.z),this.w=Math.trunc(this.w),this}negate(){return this.x=-this.x,this.y=-this.y,this.z=-this.z,this.w=-this.w,this}dot(e){return this.x*e.x+this.y*e.y+this.z*e.z+this.w*e.w}lengthSq(){return this.x*this.x+this.y*this.y+this.z*this.z+this.w*this.w}length(){return Math.sqrt(this.x*this.x+this.y*this.y+this.z*this.z+this.w*this.w)}manhattanLength(){return Math.abs(this.x)+Math.abs(this.y)+Math.abs(this.z)+Math.abs(this.w)}normalize(){return this.divideScalar(this.length()||1)}setLength(e){return this.normalize().multiplyScalar(e)}lerp(e,t){return this.x+=(e.x-this.x)*t,this.y+=(e.y-this.y)*t,this.z+=(e.z-this.z)*t,this.w+=(e.w-this.w)*t,this}lerpVectors(e,t,n){return this.x=e.x+(t.x-e.x)*n,this.y=e.y+(t.y-e.y)*n,this.z=e.z+(t.z-e.z)*n,this.w=e.w+(t.w-e.w)*n,this}equals(e){return e.x===this.x&&e.y===this.y&&e.z===this.z&&e.w===this.w}fromArray(e,t=0){return this.x=e[t],this.y=e[t+1],this.z=e[t+2],this.w=e[t+3],this}toArray(e=[],t=0){return e[t]=this.x,e[t+1]=this.y,e[t+2]=this.z,e[t+3]=this.w,e}fromBufferAttribute(e,t){return this.x=e.getX(t),this.y=e.getY(t),this.z=e.getZ(t),this.w=e.getW(t),this}random(){return this.x=Math.random(),this.y=Math.random(),this.z=Math.random(),this.w=Math.random(),this}*[Symbol.iterator](){yield this.x,yield this.y,yield this.z,yield this.w}};xa.prototype.isVector4=!0;let pt=xa;class Sc extends ei{constructor(e=1,t=1,n={}){super(),n=Object.assign({generateMipmaps:!1,internalFormat:null,minFilter:Wt,depthBuffer:!0,stencilBuffer:!1,resolveColorBuffer:!0,resolveDepthBuffer:!0,resolveStencilBuffer:!0,storeMultisampledColorBuffer:!0,storeMultisampledDepthBuffer:!0,storeMultisampledStencilBuffer:!0,depthTexture:null,samples:0,count:1,depth:1,multiview:!1,useArrayDepthTexture:!1},n),this.isRenderTarget=!0,this.width=e,this.height=t,this.depth=n.depth,this.scissor=new pt(0,0,e,t),this.scissorTest=!1,this.viewport=new pt(0,0,e,t),this.textures=[];const s=n.count;for(let r=0;r<s;r++)this.textures[r]=new zt({width:e,height:t,depth:n.depth}),this.textures[r].isRenderTargetTexture=!0,this.textures[r].renderTarget=this;this._setTextureOptions(n),this.depthBuffer=n.depthBuffer,this.stencilBuffer=n.stencilBuffer,this.resolveColorBuffer=n.resolveColorBuffer,this.resolveDepthBuffer=n.resolveDepthBuffer,this.resolveStencilBuffer=n.resolveStencilBuffer,this.storeMultisampledColorBuffer=n.storeMultisampledColorBuffer,this.storeMultisampledDepthBuffer=n.storeMultisampledDepthBuffer,this.storeMultisampledStencilBuffer=n.storeMultisampledStencilBuffer,this._depthTexture=null,this.depthTexture=n.depthTexture,this.samples=n.samples,this.multiview=n.multiview,this.useArrayDepthTexture=n.useArrayDepthTexture}_setTextureOptions(e={}){const t={minFilter:Wt,generateMipmaps:!1,flipY:!1,internalFormat:null};e.mapping!==void 0&&(t.mapping=e.mapping),e.wrapS!==void 0&&(t.wrapS=e.wrapS),e.wrapT!==void 0&&(t.wrapT=e.wrapT),e.wrapR!==void 0&&(t.wrapR=e.wrapR),e.magFilter!==void 0&&(t.magFilter=e.magFilter),e.minFilter!==void 0&&(t.minFilter=e.minFilter),e.format!==void 0&&(t.format=e.format),e.type!==void 0&&(t.type=e.type),e.anisotropy!==void 0&&(t.anisotropy=e.anisotropy),e.colorSpace!==void 0&&(t.colorSpace=e.colorSpace),e.flipY!==void 0&&(t.flipY=e.flipY),e.generateMipmaps!==void 0&&(t.generateMipmaps=e.generateMipmaps),e.mipmapsAutoUpdate!==void 0&&(t.mipmapsAutoUpdate=e.mipmapsAutoUpdate),e.internalFormat!==void 0&&(t.internalFormat=e.internalFormat);for(let n=0;n<this.textures.length;n++)this.textures[n].setValues(t)}get texture(){return this.textures[0]}set texture(e){this.textures[0]=e}set depthTexture(e){this._depthTexture!==null&&this._depthTexture.renderTarget===this&&(this._depthTexture.renderTarget=null),e!==null&&e.renderTarget===null&&(e.renderTarget=this),this._depthTexture=e}get depthTexture(){return this._depthTexture}setSize(e,t,n=1){if(this.width!==e||this.height!==t||this.depth!==n){this.width=e,this.height=t,this.depth=n;for(let s=0,r=this.textures.length;s<r;s++)this.textures[s].image.width=e,this.textures[s].image.height=t,this.textures[s].image.depth=n,this.textures[s].isData3DTexture!==!0&&(this.textures[s].isArrayTexture=this.textures[s].image.depth>1);this.dispose()}this.viewport.set(0,0,e,t),this.scissor.set(0,0,e,t)}clone(){return new this.constructor().copy(this)}copy(e){this.width=e.width,this.height=e.height,this.depth=e.depth,this.scissor.copy(e.scissor),this.scissorTest=e.scissorTest,this.viewport.copy(e.viewport),this.textures.length=0;for(let t=0,n=e.textures.length;t<n;t++){this.textures[t]=e.textures[t].clone(),this.textures[t].isRenderTargetTexture=!0,this.textures[t].renderTarget=this;const s=Object.assign({},e.textures[t].image);this.textures[t].source=new oa(s)}if(this.depthBuffer=e.depthBuffer,this.stencilBuffer=e.stencilBuffer,this.resolveColorBuffer=e.resolveColorBuffer,this.resolveDepthBuffer=e.resolveDepthBuffer,this.resolveStencilBuffer=e.resolveStencilBuffer,this.storeMultisampledColorBuffer=e.storeMultisampledColorBuffer,this.storeMultisampledDepthBuffer=e.storeMultisampledDepthBuffer,this.storeMultisampledStencilBuffer=e.storeMultisampledStencilBuffer,e.depthTexture!==null)if(e.depthTexture.renderTarget===e){const t=e.depthTexture.clone();t.renderTarget=null,this.depthTexture=t}else this.depthTexture=e.depthTexture;else this.depthTexture=null;return this.samples=e.samples,this.multiview=e.multiview,this.useArrayDepthTexture=e.useArrayDepthTexture,this}dispose(){this.dispatchEvent({type:"dispose"})}}class mn extends Sc{constructor(e=1,t=1,n={}){super(e,t,n),this.isWebGLRenderTarget=!0}}class $o extends zt{constructor(e=null,t=1,n=1,s=1){super(null),this.isDataArrayTexture=!0,this.image={data:e,width:t,height:n,depth:s},this.magFilter=Ct,this.minFilter=Ct,this.wrapR=Tn,this.generateMipmaps=!1,this.flipY=!1,this.unpackAlignment=1,this.layerUpdates=new Set}copy(e){return super.copy(e),this.wrapR=e.wrapR,this}addLayerUpdate(e){this.layerUpdates.add(e)}clearLayerUpdates(){this.layerUpdates.clear()}}class Mc extends zt{constructor(e=null,t=1,n=1,s=1){super(null),this.isData3DTexture=!0,this.image={data:e,width:t,height:n,depth:s},this.magFilter=Ct,this.minFilter=Ct,this.wrapR=Tn,this.generateMipmaps=!1,this.flipY=!1,this.unpackAlignment=1}copy(e){return super.copy(e),this.wrapR=e.wrapR,this}}const Rs=class Rs{constructor(e,t,n,s,r,a,o,d,c,m,x,f,g,l,T,p){this.elements=[1,0,0,0,0,1,0,0,0,0,1,0,0,0,0,1],e!==void 0&&this.set(e,t,n,s,r,a,o,d,c,m,x,f,g,l,T,p)}set(e,t,n,s,r,a,o,d,c,m,x,f,g,l,T,p){const u=this.elements;return u[0]=e,u[4]=t,u[8]=n,u[12]=s,u[1]=r,u[5]=a,u[9]=o,u[13]=d,u[2]=c,u[6]=m,u[10]=x,u[14]=f,u[3]=g,u[7]=l,u[11]=T,u[15]=p,this}identity(){return this.set(1,0,0,0,0,1,0,0,0,0,1,0,0,0,0,1),this}clone(){return new Rs().fromArray(this.elements)}copy(e){const t=this.elements,n=e.elements;return t[0]=n[0],t[1]=n[1],t[2]=n[2],t[3]=n[3],t[4]=n[4],t[5]=n[5],t[6]=n[6],t[7]=n[7],t[8]=n[8],t[9]=n[9],t[10]=n[10],t[11]=n[11],t[12]=n[12],t[13]=n[13],t[14]=n[14],t[15]=n[15],this}copyPosition(e){const t=this.elements,n=e.elements;return t[12]=n[12],t[13]=n[13],t[14]=n[14],this}setFromMatrix3(e){const t=e.elements;return this.set(t[0],t[3],t[6],0,t[1],t[4],t[7],0,t[2],t[5],t[8],0,0,0,0,1),this}extractBasis(e,t,n){return this.determinantAffine()===0?(e.set(1,0,0),t.set(0,1,0),n.set(0,0,1),this):(e.setFromMatrixColumn(this,0),t.setFromMatrixColumn(this,1),n.setFromMatrixColumn(this,2),this)}makeBasis(e,t,n){return this.set(e.x,t.x,n.x,0,e.y,t.y,n.y,0,e.z,t.z,n.z,0,0,0,0,1),this}extractRotation(e){if(e.determinantAffine()===0)return this.identity();const t=this.elements,n=e.elements,s=1/ii.setFromMatrixColumn(e,0).length(),r=1/ii.setFromMatrixColumn(e,1).length(),a=1/ii.setFromMatrixColumn(e,2).length();return t[0]=n[0]*s,t[1]=n[1]*s,t[2]=n[2]*s,t[3]=0,t[4]=n[4]*r,t[5]=n[5]*r,t[6]=n[6]*r,t[7]=0,t[8]=n[8]*a,t[9]=n[9]*a,t[10]=n[10]*a,t[11]=0,t[12]=0,t[13]=0,t[14]=0,t[15]=1,this}makeRotationFromEuler(e){const t=this.elements,n=e.x,s=e.y,r=e.z,a=Math.cos(n),o=Math.sin(n),d=Math.cos(s),c=Math.sin(s),m=Math.cos(r),x=Math.sin(r);if(e.order==="XYZ"){const f=a*m,g=a*x,l=o*m,T=o*x;t[0]=d*m,t[4]=-d*x,t[8]=c,t[1]=g+l*c,t[5]=f-T*c,t[9]=-o*d,t[2]=T-f*c,t[6]=l+g*c,t[10]=a*d}else if(e.order==="YXZ"){const f=d*m,g=d*x,l=c*m,T=c*x;t[0]=f+T*o,t[4]=l*o-g,t[8]=a*c,t[1]=a*x,t[5]=a*m,t[9]=-o,t[2]=g*o-l,t[6]=T+f*o,t[10]=a*d}else if(e.order==="ZXY"){const f=d*m,g=d*x,l=c*m,T=c*x;t[0]=f-T*o,t[4]=-a*x,t[8]=l+g*o,t[1]=g+l*o,t[5]=a*m,t[9]=T-f*o,t[2]=-a*c,t[6]=o,t[10]=a*d}else if(e.order==="ZYX"){const f=a*m,g=a*x,l=o*m,T=o*x;t[0]=d*m,t[4]=l*c-g,t[8]=f*c+T,t[1]=d*x,t[5]=T*c+f,t[9]=g*c-l,t[2]=-c,t[6]=o*d,t[10]=a*d}else if(e.order==="YZX"){const f=a*d,g=a*c,l=o*d,T=o*c;t[0]=d*m,t[4]=T-f*x,t[8]=l*x+g,t[1]=x,t[5]=a*m,t[9]=-o*m,t[2]=-c*m,t[6]=g*x+l,t[10]=f-T*x}else if(e.order==="XZY"){const f=a*d,g=a*c,l=o*d,T=o*c;t[0]=d*m,t[4]=-x,t[8]=c*m,t[1]=f*x+T,t[5]=a*m,t[9]=g*x-l,t[2]=l*x-g,t[6]=o*m,t[10]=T*x+f}return t[3]=0,t[7]=0,t[11]=0,t[12]=0,t[13]=0,t[14]=0,t[15]=1,this}makeRotationFromQuaternion(e){return this.compose(bc,e,yc)}lookAt(e,t,n){const s=this.elements;return Yt.subVectors(e,t),Yt.lengthSq()===0&&(Yt.z=1),Yt.normalize(),Pn.crossVectors(n,Yt),Pn.lengthSq()===0&&(Math.abs(n.z)===1?Yt.x+=1e-4:Yt.z+=1e-4,Yt.normalize(),Pn.crossVectors(n,Yt)),Pn.normalize(),Wi.crossVectors(Yt,Pn),s[0]=Pn.x,s[4]=Wi.x,s[8]=Yt.x,s[1]=Pn.y,s[5]=Wi.y,s[9]=Yt.y,s[2]=Pn.z,s[6]=Wi.z,s[10]=Yt.z,this}multiply(e){return this.multiplyMatrices(this,e)}premultiply(e){return this.multiplyMatrices(e,this)}multiplyMatrices(e,t){const n=e.elements,s=t.elements,r=this.elements,a=n[0],o=n[4],d=n[8],c=n[12],m=n[1],x=n[5],f=n[9],g=n[13],l=n[2],T=n[6],p=n[10],u=n[14],b=n[3],y=n[7],E=n[11],R=n[15],A=s[0],h=s[4],M=s[8],I=s[12],C=s[1],U=s[5],F=s[9],X=s[13],L=s[2],W=s[6],K=s[10],H=s[14],ne=s[3],q=s[7],ee=s[11],te=s[15];return r[0]=a*A+o*C+d*L+c*ne,r[4]=a*h+o*U+d*W+c*q,r[8]=a*M+o*F+d*K+c*ee,r[12]=a*I+o*X+d*H+c*te,r[1]=m*A+x*C+f*L+g*ne,r[5]=m*h+x*U+f*W+g*q,r[9]=m*M+x*F+f*K+g*ee,r[13]=m*I+x*X+f*H+g*te,r[2]=l*A+T*C+p*L+u*ne,r[6]=l*h+T*U+p*W+u*q,r[10]=l*M+T*F+p*K+u*ee,r[14]=l*I+T*X+p*H+u*te,r[3]=b*A+y*C+E*L+R*ne,r[7]=b*h+y*U+E*W+R*q,r[11]=b*M+y*F+E*K+R*ee,r[15]=b*I+y*X+E*H+R*te,this}multiplyScalar(e){const t=this.elements;return t[0]*=e,t[4]*=e,t[8]*=e,t[12]*=e,t[1]*=e,t[5]*=e,t[9]*=e,t[13]*=e,t[2]*=e,t[6]*=e,t[10]*=e,t[14]*=e,t[3]*=e,t[7]*=e,t[11]*=e,t[15]*=e,this}determinant(){const e=this.elements,t=e[0],n=e[4],s=e[8],r=e[12],a=e[1],o=e[5],d=e[9],c=e[13],m=e[2],x=e[6],f=e[10],g=e[14],l=e[3],T=e[7],p=e[11],u=e[15],b=d*g-c*f,y=o*g-c*x,E=o*f-d*x,R=a*g-c*m,A=a*f-d*m,h=a*x-o*m;return t*(T*b-p*y+u*E)-n*(l*b-p*R+u*A)+s*(l*y-T*R+u*h)-r*(l*E-T*A+p*h)}determinantAffine(){const e=this.elements,t=e[0],n=e[4],s=e[8],r=e[1],a=e[5],o=e[9],d=e[2],c=e[6],m=e[10];return t*(a*m-o*c)-n*(r*m-o*d)+s*(r*c-a*d)}transpose(){const e=this.elements;let t;return t=e[1],e[1]=e[4],e[4]=t,t=e[2],e[2]=e[8],e[8]=t,t=e[6],e[6]=e[9],e[9]=t,t=e[3],e[3]=e[12],e[12]=t,t=e[7],e[7]=e[13],e[13]=t,t=e[11],e[11]=e[14],e[14]=t,this}setPosition(e,t,n){const s=this.elements;return e.isVector3?(s[12]=e.x,s[13]=e.y,s[14]=e.z):(s[12]=e,s[13]=t,s[14]=n),this}invert(){const e=this.elements,t=e[0],n=e[1],s=e[2],r=e[3],a=e[4],o=e[5],d=e[6],c=e[7],m=e[8],x=e[9],f=e[10],g=e[11],l=e[12],T=e[13],p=e[14],u=e[15],b=t*o-n*a,y=t*d-s*a,E=t*c-r*a,R=n*d-s*o,A=n*c-r*o,h=s*c-r*d,M=m*T-x*l,I=m*p-f*l,C=m*u-g*l,U=x*p-f*T,F=x*u-g*T,X=f*u-g*p,L=b*X-y*F+E*U+R*C-A*I+h*M;if(L===0)return this.set(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);const W=1/L;return e[0]=(o*X-d*F+c*U)*W,e[1]=(s*F-n*X-r*U)*W,e[2]=(T*h-p*A+u*R)*W,e[3]=(f*A-x*h-g*R)*W,e[4]=(d*C-a*X-c*I)*W,e[5]=(t*X-s*C+r*I)*W,e[6]=(p*E-l*h-u*y)*W,e[7]=(m*h-f*E+g*y)*W,e[8]=(a*F-o*C+c*M)*W,e[9]=(n*C-t*F-r*M)*W,e[10]=(l*A-T*E+u*b)*W,e[11]=(x*E-m*A-g*b)*W,e[12]=(o*I-a*U-d*M)*W,e[13]=(t*U-n*I+s*M)*W,e[14]=(T*y-l*R-p*b)*W,e[15]=(m*R-x*y+f*b)*W,this}scale(e){const t=this.elements,n=e.x,s=e.y,r=e.z;return t[0]*=n,t[4]*=s,t[8]*=r,t[1]*=n,t[5]*=s,t[9]*=r,t[2]*=n,t[6]*=s,t[10]*=r,t[3]*=n,t[7]*=s,t[11]*=r,this}getMaxScaleOnAxis(){const e=this.elements,t=e[0]*e[0]+e[1]*e[1]+e[2]*e[2],n=e[4]*e[4]+e[5]*e[5]+e[6]*e[6],s=e[8]*e[8]+e[9]*e[9]+e[10]*e[10];return Math.sqrt(Math.max(t,n,s))}makeTranslation(e,t,n){return e.isVector3?this.set(1,0,0,e.x,0,1,0,e.y,0,0,1,e.z,0,0,0,1):this.set(1,0,0,e,0,1,0,t,0,0,1,n,0,0,0,1),this}makeRotationX(e){const t=Math.cos(e),n=Math.sin(e);return this.set(1,0,0,0,0,t,-n,0,0,n,t,0,0,0,0,1),this}makeRotationY(e){const t=Math.cos(e),n=Math.sin(e);return this.set(t,0,n,0,0,1,0,0,-n,0,t,0,0,0,0,1),this}makeRotationZ(e){const t=Math.cos(e),n=Math.sin(e);return this.set(t,-n,0,0,n,t,0,0,0,0,1,0,0,0,0,1),this}makeRotationAxis(e,t){const n=Math.cos(t),s=Math.sin(t),r=1-n,a=e.x,o=e.y,d=e.z,c=r*a,m=r*o;return this.set(c*a+n,c*o-s*d,c*d+s*o,0,c*o+s*d,m*o+n,m*d-s*a,0,c*d-s*o,m*d+s*a,r*d*d+n,0,0,0,0,1),this}makeScale(e,t,n){return this.set(e,0,0,0,0,t,0,0,0,0,n,0,0,0,0,1),this}makeShear(e,t,n,s,r,a){return this.set(1,n,r,0,e,1,a,0,t,s,1,0,0,0,0,1),this}compose(e,t,n){const s=this.elements,r=t._x,a=t._y,o=t._z,d=t._w,c=r+r,m=a+a,x=o+o,f=r*c,g=r*m,l=r*x,T=a*m,p=a*x,u=o*x,b=d*c,y=d*m,E=d*x,R=n.x,A=n.y,h=n.z;return s[0]=(1-(T+u))*R,s[1]=(g+E)*R,s[2]=(l-y)*R,s[3]=0,s[4]=(g-E)*A,s[5]=(1-(f+u))*A,s[6]=(p+b)*A,s[7]=0,s[8]=(l+y)*h,s[9]=(p-b)*h,s[10]=(1-(f+T))*h,s[11]=0,s[12]=e.x,s[13]=e.y,s[14]=e.z,s[15]=1,this}decompose(e,t,n){const s=this.elements;e.x=s[12],e.y=s[13],e.z=s[14];const r=this.determinantAffine();if(r===0)return n.set(1,1,1),t.identity(),this;let a=ii.set(s[0],s[1],s[2]).length();const o=ii.set(s[4],s[5],s[6]).length(),d=ii.set(s[8],s[9],s[10]).length();r<0&&(a=-a),tn.copy(this);const c=1/a,m=1/o,x=1/d;return tn.elements[0]*=c,tn.elements[1]*=c,tn.elements[2]*=c,tn.elements[4]*=m,tn.elements[5]*=m,tn.elements[6]*=m,tn.elements[8]*=x,tn.elements[9]*=x,tn.elements[10]*=x,t.setFromRotationMatrix(tn),n.x=a,n.y=o,n.z=d,this}makePerspective(e,t,n,s,r,a,o=ln,d=!1){const c=this.elements,m=2*r/(t-e),x=2*r/(n-s),f=(t+e)/(t-e),g=(n+s)/(n-s);let l,T;if(d)l=r/(a-r),T=a*r/(a-r);else if(o===ln)l=-(a+r)/(a-r),T=-2*a*r/(a-r);else if(o===Ui)l=-a/(a-r),T=-a*r/(a-r);else throw new Error("THREE.Matrix4.makePerspective(): Invalid coordinate system: "+o);return c[0]=m,c[4]=0,c[8]=f,c[12]=0,c[1]=0,c[5]=x,c[9]=g,c[13]=0,c[2]=0,c[6]=0,c[10]=l,c[14]=T,c[3]=0,c[7]=0,c[11]=-1,c[15]=0,this}makeOrthographic(e,t,n,s,r,a,o=ln,d=!1){const c=this.elements,m=2/(t-e),x=2/(n-s),f=-(t+e)/(t-e),g=-(n+s)/(n-s);let l,T;if(d)l=1/(a-r),T=a/(a-r);else if(o===ln)l=-2/(a-r),T=-(a+r)/(a-r);else if(o===Ui)l=-1/(a-r),T=-r/(a-r);else throw new Error("THREE.Matrix4.makeOrthographic(): Invalid coordinate system: "+o);return c[0]=m,c[4]=0,c[8]=0,c[12]=f,c[1]=0,c[5]=x,c[9]=0,c[13]=g,c[2]=0,c[6]=0,c[10]=l,c[14]=T,c[3]=0,c[7]=0,c[11]=0,c[15]=1,this}equals(e){const t=this.elements,n=e.elements;for(let s=0;s<16;s++)if(t[s]!==n[s])return!1;return!0}fromArray(e,t=0){for(let n=0;n<16;n++)this.elements[n]=e[n+t];return this}toArray(e=[],t=0){const n=this.elements;return e[t]=n[0],e[t+1]=n[1],e[t+2]=n[2],e[t+3]=n[3],e[t+4]=n[4],e[t+5]=n[5],e[t+6]=n[6],e[t+7]=n[7],e[t+8]=n[8],e[t+9]=n[9],e[t+10]=n[10],e[t+11]=n[11],e[t+12]=n[12],e[t+13]=n[13],e[t+14]=n[14],e[t+15]=n[15],e}};Rs.prototype.isMatrix4=!0;let mt=Rs;const ii=new z,tn=new mt,bc=new z(0,0,0),yc=new z(1,1,1),Pn=new z,Wi=new z,Yt=new z,za=new mt,Ga=new Mi;class Gn{constructor(e=0,t=0,n=0,s=Gn.DEFAULT_ORDER){this.isEuler=!0,this._x=e,this._y=t,this._z=n,this._order=s}get x(){return this._x}set x(e){this._x=e,this._onChangeCallback()}get y(){return this._y}set y(e){this._y=e,this._onChangeCallback()}get z(){return this._z}set z(e){this._z=e,this._onChangeCallback()}get order(){return this._order}set order(e){this._order=e,this._onChangeCallback()}set(e,t,n,s=this._order){return this._x=e,this._y=t,this._z=n,this._order=s,this._onChangeCallback(),this}clone(){return new this.constructor(this._x,this._y,this._z,this._order)}copy(e){return this._x=e._x,this._y=e._y,this._z=e._z,this._order=e._order,this._onChangeCallback(),this}setFromRotationMatrix(e,t=this._order,n=!0){const s=e.elements,r=s[0],a=s[4],o=s[8],d=s[1],c=s[5],m=s[9],x=s[2],f=s[6],g=s[10];switch(t){case"XYZ":this._y=Math.asin($e(o,-1,1)),Math.abs(o)<.9999999?(this._x=Math.atan2(-m,g),this._z=Math.atan2(-a,r)):(this._x=Math.atan2(f,c),this._z=0);break;case"YXZ":this._x=Math.asin(-$e(m,-1,1)),Math.abs(m)<.9999999?(this._y=Math.atan2(o,g),this._z=Math.atan2(d,c)):(this._y=Math.atan2(-x,r),this._z=0);break;case"ZXY":this._x=Math.asin($e(f,-1,1)),Math.abs(f)<.9999999?(this._y=Math.atan2(-x,g),this._z=Math.atan2(-a,c)):(this._y=0,this._z=Math.atan2(d,r));break;case"ZYX":this._y=Math.asin(-$e(x,-1,1)),Math.abs(x)<.9999999?(this._x=Math.atan2(f,g),this._z=Math.atan2(d,r)):(this._x=0,this._z=Math.atan2(-a,c));break;case"YZX":this._z=Math.asin($e(d,-1,1)),Math.abs(d)<.9999999?(this._x=Math.atan2(-m,c),this._y=Math.atan2(-x,r)):(this._x=0,this._y=Math.atan2(o,g));break;case"XZY":this._z=Math.asin(-$e(a,-1,1)),Math.abs(a)<.9999999?(this._x=Math.atan2(f,c),this._y=Math.atan2(o,r)):(this._x=Math.atan2(-m,g),this._y=0);break;default:De("Euler: .setFromRotationMatrix() encountered an unknown order: "+t)}return this._order=t,n===!0&&this._onChangeCallback(),this}setFromQuaternion(e,t,n){return za.makeRotationFromQuaternion(e),this.setFromRotationMatrix(za,t,n)}setFromVector3(e,t=this._order){return this.set(e.x,e.y,e.z,t)}reorder(e){return Ga.setFromEuler(this),this.setFromQuaternion(Ga,e)}equals(e){return e._x===this._x&&e._y===this._y&&e._z===this._z&&e._order===this._order}fromArray(e){return this._x=e[0],this._y=e[1],this._z=e[2],e[3]!==void 0&&(this._order=e[3]),this._onChangeCallback(),this}toArray(e=[],t=0){return e[t]=this._x,e[t+1]=this._y,e[t+2]=this._z,e[t+3]=this._order,e}_onChange(e){return this._onChangeCallback=e,this}_onChangeCallback(){}*[Symbol.iterator](){yield this._x,yield this._y,yield this._z,yield this._order}}Gn.DEFAULT_ORDER="XYZ";class Jo{constructor(){this.mask=1}set(e){this.mask=(1<<e|0)>>>0}enable(e){this.mask|=1<<e|0}enableAll(){this.mask=-1}toggle(e){this.mask^=1<<e|0}disable(e){this.mask&=~(1<<e|0)}disableAll(){this.mask=0}test(e){return(this.mask&e.mask)!==0}isEnabled(e){return(this.mask&(1<<e|0))!==0}}let Ec=0;const Ha=new z,si=new Mi,vn=new mt,Xi=new z,Ei=new z,Tc=new z,wc=new Mi,Va=new z(1,0,0),Wa=new z(0,1,0),Xa=new z(0,0,1),qa={type:"added"},Ac={type:"removed"},ri={type:"childadded",child:null},Ws={type:"childremoved",child:null};class Ut extends ei{constructor(){super(),this.isObject3D=!0,Object.defineProperty(this,"id",{value:Ec++}),this.uuid=Oi(),this.name="",this.type="Object3D",this.parent=null,this.children=[],this.up=Ut.DEFAULT_UP.clone();const e=new z,t=new Gn,n=new Mi,s=new z(1,1,1);function r(){n.setFromEuler(t,!1)}function a(){t.setFromQuaternion(n,void 0,!1)}t._onChange(r),n._onChange(a),Object.defineProperties(this,{position:{configurable:!0,enumerable:!0,value:e},rotation:{configurable:!0,enumerable:!0,value:t},quaternion:{configurable:!0,enumerable:!0,value:n},scale:{configurable:!0,enumerable:!0,value:s},modelViewMatrix:{value:new mt},normalMatrix:{value:new Ne}}),this.matrix=new mt,this.matrixWorld=new mt,this.matrixAutoUpdate=Ut.DEFAULT_MATRIX_AUTO_UPDATE,this.matrixWorldAutoUpdate=Ut.DEFAULT_MATRIX_WORLD_AUTO_UPDATE,this.matrixWorldNeedsUpdate=!1,this.layers=new Jo,this.visible=!0,this.castShadow=!1,this.receiveShadow=!1,this.frustumCulled=!0,this.renderOrder=0,this.animations=[],this.customDepthMaterial=void 0,this.customDistanceMaterial=void 0,this.static=!1,this.userData={},this.pivot=null}onBeforeShadow(){}onAfterShadow(){}onBeforeRender(){}onAfterRender(){}applyMatrix4(e){this.matrixAutoUpdate&&this.updateMatrix(),this.matrix.premultiply(e),this.matrix.decompose(this.position,this.quaternion,this.scale),this.pivot!==null&&this.position.copy(this.pivot).applyMatrix4(this.matrix).sub(this.pivot)}applyQuaternion(e){return this.quaternion.premultiply(e),this}setRotationFromAxisAngle(e,t){this.quaternion.setFromAxisAngle(e,t)}setRotationFromEuler(e){this.quaternion.setFromEuler(e,!0)}setRotationFromMatrix(e){this.quaternion.setFromRotationMatrix(e)}setRotationFromQuaternion(e){this.quaternion.copy(e)}rotateOnAxis(e,t){return si.setFromAxisAngle(e,t),this.quaternion.multiply(si),this}rotateOnWorldAxis(e,t){return si.setFromAxisAngle(e,t),this.quaternion.premultiply(si),this}rotateX(e){return this.rotateOnAxis(Va,e)}rotateY(e){return this.rotateOnAxis(Wa,e)}rotateZ(e){return this.rotateOnAxis(Xa,e)}translateOnAxis(e,t){return Ha.copy(e).applyQuaternion(this.quaternion),this.position.add(Ha.multiplyScalar(t)),this}translateX(e){return this.translateOnAxis(Va,e)}translateY(e){return this.translateOnAxis(Wa,e)}translateZ(e){return this.translateOnAxis(Xa,e)}localToWorld(e){return this.updateWorldMatrix(!0,!1),e.applyMatrix4(this.matrixWorld)}worldToLocal(e){return this.updateWorldMatrix(!0,!1),e.applyMatrix4(vn.copy(this.matrixWorld).invert())}lookAt(e,t,n){e.isVector3?Xi.copy(e):Xi.set(e,t,n);const s=this.parent;this.updateWorldMatrix(!0,!1),Ei.setFromMatrixPosition(this.matrixWorld),this.isCamera||this.isLight?vn.lookAt(Ei,Xi,this.up):vn.lookAt(Xi,Ei,this.up),this.quaternion.setFromRotationMatrix(vn),s&&(vn.extractRotation(s.matrixWorld),si.setFromRotationMatrix(vn),this.quaternion.premultiply(si.invert()))}add(e){if(arguments.length>1){for(let t=0;t<arguments.length;t++)this.add(arguments[t]);return this}return e===this?(nt("Object3D.add: object can't be added as a child of itself.",e),this):(e&&e.isObject3D?(e.removeFromParent(),e.parent=this,this.children.push(e),e.dispatchEvent(qa),ri.child=e,this.dispatchEvent(ri),ri.child=null):nt("Object3D.add: object not an instance of THREE.Object3D.",e),this)}remove(e){if(arguments.length>1){for(let n=0;n<arguments.length;n++)this.remove(arguments[n]);return this}const t=this.children.indexOf(e);return t!==-1&&(e.parent=null,this.children.splice(t,1),e.dispatchEvent(Ac),Ws.child=e,this.dispatchEvent(Ws),Ws.child=null),this}removeFromParent(){const e=this.parent;return e!==null&&e.remove(this),this}clear(){return this.remove(...this.children)}attach(e){return this.updateWorldMatrix(!0,!1),vn.copy(this.matrixWorld).invert(),e.parent!==null&&(e.parent.updateWorldMatrix(!0,!1),vn.multiply(e.parent.matrixWorld)),e.applyMatrix4(vn),e.removeFromParent(),e.parent=this,this.children.push(e),e.updateWorldMatrix(!1,!0),e.dispatchEvent(qa),ri.child=e,this.dispatchEvent(ri),ri.child=null,this}getObjectById(e){return this.getObjectByProperty("id",e)}getObjectByName(e){return this.getObjectByProperty("name",e)}getObjectByProperty(e,t){if(this[e]===t)return this;for(let n=0,s=this.children.length;n<s;n++){const a=this.children[n].getObjectByProperty(e,t);if(a!==void 0)return a}}getObjectsByProperty(e,t,n=[]){this[e]===t&&n.push(this);const s=this.children;for(let r=0,a=s.length;r<a;r++)s[r].getObjectsByProperty(e,t,n);return n}getWorldPosition(e){return this.updateWorldMatrix(!0,!1),e.setFromMatrixPosition(this.matrixWorld)}getWorldQuaternion(e){return this.updateWorldMatrix(!0,!1),this.matrixWorld.decompose(Ei,e,Tc),e}getWorldScale(e){return this.updateWorldMatrix(!0,!1),this.matrixWorld.decompose(Ei,wc,e),e}getWorldDirection(e){this.updateWorldMatrix(!0,!1);const t=this.matrixWorld.elements;return e.set(t[8],t[9],t[10]).normalize()}raycast(){}intersectsFrustum(){return!0}traverse(e){e(this);const t=this.children;for(let n=0,s=t.length;n<s;n++)t[n].traverse(e)}traverseVisible(e){if(this.visible===!1)return;e(this);const t=this.children;for(let n=0,s=t.length;n<s;n++)t[n].traverseVisible(e)}traverseAncestors(e){const t=this.parent;t!==null&&(e(t),t.traverseAncestors(e))}updateMatrix(){this.matrix.compose(this.position,this.quaternion,this.scale);const e=this.pivot;if(e!==null){const t=e.x,n=e.y,s=e.z,r=this.matrix.elements;r[12]+=t-r[0]*t-r[4]*n-r[8]*s,r[13]+=n-r[1]*t-r[5]*n-r[9]*s,r[14]+=s-r[2]*t-r[6]*n-r[10]*s}this.matrixWorldNeedsUpdate=!0}updateMatrixWorld(e){this.matrixAutoUpdate&&this.updateMatrix(),(this.matrixWorldNeedsUpdate||e)&&(this.matrixWorldAutoUpdate===!0&&(this.parent===null?this.matrixWorld.copy(this.matrix):this.matrixWorld.multiplyMatrices(this.parent.matrixWorld,this.matrix)),this.matrixWorldNeedsUpdate=!1,e=!0);const t=this.children;for(let n=0,s=t.length;n<s;n++)t[n].updateMatrixWorld(e)}updateWorldMatrix(e,t,n=!1){const s=this.parent;if(e===!0&&s!==null&&s.updateWorldMatrix(!0,!1),this.matrixAutoUpdate&&this.updateMatrix(),(this.matrixWorldNeedsUpdate||n)&&(this.matrixWorldAutoUpdate===!0&&(this.parent===null?this.matrixWorld.copy(this.matrix):this.matrixWorld.multiplyMatrices(this.parent.matrixWorld,this.matrix)),this.matrixWorldNeedsUpdate=!1,n=!0),t===!0){const r=this.children;for(let a=0,o=r.length;a<o;a++)r[a].updateWorldMatrix(!1,!0,n)}}toJSON(e){const t=e===void 0||typeof e=="string",n={};t&&(e={geometries:{},materials:{},textures:{},images:{},shapes:{},skeletons:{},animations:{},nodes:{}},n.metadata={version:4.7,type:"Object",generator:"Object3D.toJSON"});const s={};s.uuid=this.uuid,s.type=this.type,s.name=this.name,s.castShadow=this.castShadow,s.receiveShadow=this.receiveShadow,s.visible=this.visible,s.frustumCulled=this.frustumCulled,s.renderOrder=this.renderOrder,s.static=this.static,s.matrixAutoUpdate=this.matrixAutoUpdate,Object.keys(this.userData).length>0&&(s.userData=this.userData),s.layers=this.layers.mask,s.matrix=this.matrix.toArray(),s.up=this.up.toArray(),this.pivot!==null&&(s.pivot=this.pivot.toArray()),this.morphTargetDictionary!==void 0&&(s.morphTargetDictionary=Object.assign({},this.morphTargetDictionary)),this.morphTargetInfluences!==void 0&&(s.morphTargetInfluences=this.morphTargetInfluences.slice()),this.isInstancedMesh&&(s.type="InstancedMesh",s.count=this.count,s.instanceMatrix=this.instanceMatrix.toJSON(),this.instanceColor!==null&&(s.instanceColor=this.instanceColor.toJSON())),this.isBatchedMesh&&(s.type="BatchedMesh",s.perObjectFrustumCulled=this.perObjectFrustumCulled,s.sortObjects=this.sortObjects,s.drawRanges=this._drawRanges,s.reservedRanges=this._reservedRanges,s.geometryInfo=this._geometryInfo.map(o=>({...o,boundingBox:o.boundingBox?o.boundingBox.toJSON():void 0,boundingSphere:o.boundingSphere?o.boundingSphere.toJSON():void 0})),s.instanceInfo=this._instanceInfo.map(o=>({...o})),s.availableInstanceIds=this._availableInstanceIds.slice(),s.availableGeometryIds=this._availableGeometryIds.slice(),s.nextIndexStart=this._nextIndexStart,s.nextVertexStart=this._nextVertexStart,s.geometryCount=this._geometryCount,s.maxInstanceCount=this._maxInstanceCount,s.maxVertexCount=this._maxVertexCount,s.maxIndexCount=this._maxIndexCount,s.geometryInitialized=this._geometryInitialized,s.matricesTexture=this._matricesTexture.toJSON(e),s.indirectTexture=this._indirectTexture.toJSON(e),this._colorsTexture!==null&&(s.colorsTexture=this._colorsTexture.toJSON(e)),this.boundingSphere!==null&&(s.boundingSphere=this.boundingSphere.toJSON()),this.boundingBox!==null&&(s.boundingBox=this.boundingBox.toJSON()));function r(o,d){return o[d.uuid]===void 0&&(o[d.uuid]=d.toJSON(e)),d.uuid}if(this.isScene)this.background&&(this.background.isColor?s.background=this.background.toJSON():this.background.isTexture&&(s.background=this.background.toJSON(e).uuid)),this.environment&&this.environment.isTexture&&this.environment.isRenderTargetTexture!==!0&&(s.environment=this.environment.toJSON(e).uuid);else if(this.isMesh||this.isLine||this.isPoints){s.geometry=r(e.geometries,this.geometry);const o=this.geometry.parameters;if(o!==void 0&&o.shapes!==void 0){const d=o.shapes;if(Array.isArray(d))for(let c=0,m=d.length;c<m;c++){const x=d[c];r(e.shapes,x)}else r(e.shapes,d)}}if(this.isSkinnedMesh&&(s.bindMode=this.bindMode,s.bindMatrix=this.bindMatrix.toArray(),this.skeleton!==void 0&&(r(e.skeletons,this.skeleton),s.skeleton=this.skeleton.uuid)),this.material!==void 0)if(Array.isArray(this.material)){const o=[];for(let d=0,c=this.material.length;d<c;d++)o.push(r(e.materials,this.material[d]));s.material=o}else s.material=r(e.materials,this.material);if(this.children.length>0){s.children=[];for(let o=0;o<this.children.length;o++)s.children.push(this.children[o].toJSON(e).object)}if(this.animations.length>0){s.animations=[];for(let o=0;o<this.animations.length;o++){const d=this.animations[o];s.animations.push(r(e.animations,d))}}if(t){const o=a(e.geometries),d=a(e.materials),c=a(e.textures),m=a(e.images),x=a(e.shapes),f=a(e.skeletons),g=a(e.animations),l=a(e.nodes);o.length>0&&(n.geometries=o),d.length>0&&(n.materials=d),c.length>0&&(n.textures=c),m.length>0&&(n.images=m),x.length>0&&(n.shapes=x),f.length>0&&(n.skeletons=f),g.length>0&&(n.animations=g),l.length>0&&(n.nodes=l)}return n.object=s,n;function a(o){const d=[];for(const c in o){const m=o[c];delete m.metadata,d.push(m)}return d}}clone(e){return new this.constructor().copy(this,e)}copy(e,t=!0){if(this.name=e.name,this.up.copy(e.up),this.position.copy(e.position),this.rotation.order=e.rotation.order,this.quaternion.copy(e.quaternion),this.scale.copy(e.scale),this.pivot=e.pivot!==null?e.pivot.clone():null,this.matrix.copy(e.matrix),this.matrixWorld.copy(e.matrixWorld),this.matrixAutoUpdate=e.matrixAutoUpdate,this.matrixWorldAutoUpdate=e.matrixWorldAutoUpdate,this.matrixWorldNeedsUpdate=e.matrixWorldNeedsUpdate,this.layers.mask=e.layers.mask,this.visible=e.visible,this.castShadow=e.castShadow,this.receiveShadow=e.receiveShadow,this.frustumCulled=e.frustumCulled,this.renderOrder=e.renderOrder,this.static=e.static,this.animations=e.animations.slice(),this.userData=JSON.parse(JSON.stringify(e.userData)),t===!0)for(let n=0;n<e.children.length;n++){const s=e.children[n];this.add(s.clone())}return this}dispose(){this.dispatchEvent({type:"dispose"})}}Ut.DEFAULT_UP=new z(0,1,0);Ut.DEFAULT_MATRIX_AUTO_UPDATE=!0;Ut.DEFAULT_MATRIX_WORLD_AUTO_UPDATE=!0;class kn extends Ut{constructor(){super(),this.isGroup=!0,this.type="Group"}}const Rc={type:"move"};class Xs{constructor(){this._targetRay=null,this._grip=null,this._hand=null}getHandSpace(){return this._hand===null&&(this._hand=new kn,this._hand.matrixAutoUpdate=!1,this._hand.visible=!1,this._hand.joints={},this._hand.inputState={pinching:!1}),this._hand}getTargetRaySpace(){return this._targetRay===null&&(this._targetRay=new kn,this._targetRay.matrixAutoUpdate=!1,this._targetRay.visible=!1,this._targetRay.hasLinearVelocity=!1,this._targetRay.linearVelocity=new z,this._targetRay.hasAngularVelocity=!1,this._targetRay.angularVelocity=new z),this._targetRay}getGripSpace(){return this._grip===null&&(this._grip=new kn,this._grip.matrixAutoUpdate=!1,this._grip.visible=!1,this._grip.hasLinearVelocity=!1,this._grip.linearVelocity=new z,this._grip.hasAngularVelocity=!1,this._grip.angularVelocity=new z,this._grip.eventsEnabled=!1),this._grip}dispatchEvent(e){return this._targetRay!==null&&this._targetRay.dispatchEvent(e),this._grip!==null&&this._grip.dispatchEvent(e),this._hand!==null&&this._hand.dispatchEvent(e),this}connect(e){if(e&&e.hand){const t=this._hand;if(t)for(const n of e.hand.values())this._getHandJoint(t,n)}return this.dispatchEvent({type:"connected",data:e}),this}disconnect(e){return this.dispatchEvent({type:"disconnected",data:e}),this._targetRay!==null&&(this._targetRay.visible=!1),this._grip!==null&&(this._grip.visible=!1),this._hand!==null&&(this._hand.visible=!1),this}update(e,t,n){let s=null,r=null,a=null;const o=this._targetRay,d=this._grip,c=this._hand;if(e&&t.session.visibilityState!=="visible-blurred"){if(c&&e.hand){a=!0;for(const T of e.hand.values()){const p=t.getJointPose(T,n),u=this._getHandJoint(c,T);p!==null&&(u.matrix.fromArray(p.transform.matrix),u.matrix.decompose(u.position,u.quaternion,u.scale),u.matrixWorldNeedsUpdate=!0,u.jointRadius=p.radius),u.visible=p!==null}const m=c.joints["index-finger-tip"],x=c.joints["thumb-tip"],f=m.position.distanceTo(x.position),g=.02,l=.005;c.inputState.pinching&&f>g+l?(c.inputState.pinching=!1,this.dispatchEvent({type:"pinchend",handedness:e.handedness,target:this})):!c.inputState.pinching&&f<=g-l&&(c.inputState.pinching=!0,this.dispatchEvent({type:"pinchstart",handedness:e.handedness,target:this}))}else d!==null&&e.gripSpace&&(r=t.getPose(e.gripSpace,n),r!==null&&(d.matrix.fromArray(r.transform.matrix),d.matrix.decompose(d.position,d.quaternion,d.scale),d.matrixWorldNeedsUpdate=!0,r.linearVelocity?(d.hasLinearVelocity=!0,d.linearVelocity.copy(r.linearVelocity)):d.hasLinearVelocity=!1,r.angularVelocity?(d.hasAngularVelocity=!0,d.angularVelocity.copy(r.angularVelocity)):d.hasAngularVelocity=!1,d.eventsEnabled&&d.dispatchEvent({type:"gripUpdated",data:e,target:this})));o!==null&&(s=t.getPose(e.targetRaySpace,n),s===null&&r!==null&&(s=r),s!==null&&(o.matrix.fromArray(s.transform.matrix),o.matrix.decompose(o.position,o.quaternion,o.scale),o.matrixWorldNeedsUpdate=!0,s.linearVelocity?(o.hasLinearVelocity=!0,o.linearVelocity.copy(s.linearVelocity)):o.hasLinearVelocity=!1,s.angularVelocity?(o.hasAngularVelocity=!0,o.angularVelocity.copy(s.angularVelocity)):o.hasAngularVelocity=!1,this.dispatchEvent(Rc)))}return o!==null&&(o.visible=s!==null),d!==null&&(d.visible=r!==null),c!==null&&(c.visible=a!==null),this}_getHandJoint(e,t){if(e.joints[t.jointName]===void 0){const n=new kn;n.matrixAutoUpdate=!1,n.visible=!1,e.joints[t.jointName]=n,e.add(n)}return e.joints[t.jointName]}}const Qo={aliceblue:15792383,antiquewhite:16444375,aqua:65535,aquamarine:8388564,azure:15794175,beige:16119260,bisque:16770244,black:0,blanchedalmond:16772045,blue:255,blueviolet:9055202,brown:10824234,burlywood:14596231,cadetblue:6266528,chartreuse:8388352,chocolate:13789470,coral:16744272,cornflowerblue:6591981,cornsilk:16775388,crimson:14423100,cyan:65535,darkblue:139,darkcyan:35723,darkgoldenrod:12092939,darkgray:11119017,darkgreen:25600,darkgrey:11119017,darkkhaki:12433259,darkmagenta:9109643,darkolivegreen:5597999,darkorange:16747520,darkorchid:10040012,darkred:9109504,darksalmon:15308410,darkseagreen:9419919,darkslateblue:4734347,darkslategray:3100495,darkslategrey:3100495,darkturquoise:52945,darkviolet:9699539,deeppink:16716947,deepskyblue:49151,dimgray:6908265,dimgrey:6908265,dodgerblue:2003199,firebrick:11674146,floralwhite:16775920,forestgreen:2263842,fuchsia:16711935,gainsboro:14474460,ghostwhite:16316671,gold:16766720,goldenrod:14329120,gray:8421504,green:32768,greenyellow:11403055,grey:8421504,honeydew:15794160,hotpink:16738740,indianred:13458524,indigo:4915330,ivory:16777200,khaki:15787660,lavender:15132410,lavenderblush:16773365,lawngreen:8190976,lemonchiffon:16775885,lightblue:11393254,lightcoral:15761536,lightcyan:14745599,lightgoldenrodyellow:16448210,lightgray:13882323,lightgreen:9498256,lightgrey:13882323,lightpink:16758465,lightsalmon:16752762,lightseagreen:2142890,lightskyblue:8900346,lightslategray:7833753,lightslategrey:7833753,lightsteelblue:11584734,lightyellow:16777184,lime:65280,limegreen:3329330,linen:16445670,magenta:16711935,maroon:8388608,mediumaquamarine:6737322,mediumblue:205,mediumorchid:12211667,mediumpurple:9662683,mediumseagreen:3978097,mediumslateblue:8087790,mediumspringgreen:64154,mediumturquoise:4772300,mediumvioletred:13047173,midnightblue:1644912,mintcream:16121850,mistyrose:16770273,moccasin:16770229,navajowhite:16768685,navy:128,oldlace:16643558,olive:8421376,olivedrab:7048739,orange:16753920,orangered:16729344,orchid:14315734,palegoldenrod:15657130,palegreen:10025880,paleturquoise:11529966,palevioletred:14381203,papayawhip:16773077,peachpuff:16767673,peru:13468991,pink:16761035,plum:14524637,powderblue:11591910,purple:8388736,rebeccapurple:6697881,red:16711680,rosybrown:12357519,royalblue:4286945,saddlebrown:9127187,salmon:16416882,sandybrown:16032864,seagreen:3050327,seashell:16774638,sienna:10506797,silver:12632256,skyblue:8900331,slateblue:6970061,slategray:7372944,slategrey:7372944,snow:16775930,springgreen:65407,steelblue:4620980,tan:13808780,teal:32896,thistle:14204888,tomato:16737095,turquoise:4251856,violet:15631086,wheat:16113331,white:16777215,whitesmoke:16119285,yellow:16776960,yellowgreen:10145074},Ln={h:0,s:0,l:0},qi={h:0,s:0,l:0};function qs(i,e,t){return t<0&&(t+=1),t>1&&(t-=1),t<1/6?i+(e-i)*6*t:t<1/2?e:t<2/3?i+(e-i)*6*(2/3-t):i}class je{constructor(e,t,n){return this.isColor=!0,this.r=1,this.g=1,this.b=1,this.set(e,t,n)}set(e,t,n){if(t===void 0&&n===void 0){const s=e;s&&s.isColor?this.copy(s):typeof s=="number"?this.setHex(s):typeof s=="string"&&this.setStyle(s)}else this.setRGB(e,t,n);return this}setScalar(e){return this.r=e,this.g=e,this.b=e,this}setHex(e,t=Jt){return e=Math.floor(e),this.r=(e>>16&255)/255,this.g=(e>>8&255)/255,this.b=(e&255)/255,Ze.colorSpaceToWorking(this,t),this}setRGB(e,t,n,s=Ze.workingColorSpace){return this.r=e,this.g=t,this.b=n,Ze.colorSpaceToWorking(this,s),this}setHSL(e,t,n,s=Ze.workingColorSpace){if(e=fc(e,1),t=$e(t,0,1),n=$e(n,0,1),t===0)this.r=this.g=this.b=n;else{const r=n<=.5?n*(1+t):n+t-n*t,a=2*n-r;this.r=qs(a,r,e+1/3),this.g=qs(a,r,e),this.b=qs(a,r,e-1/3)}return Ze.colorSpaceToWorking(this,s),this}setStyle(e,t=Jt){function n(r){r!==void 0&&parseFloat(r)<1&&De("Color: Alpha component of "+e+" will be ignored.")}let s;if(s=/^(\w+)\(([^\)]*)\)/.exec(e)){let r;const a=s[1],o=s[2];switch(a){case"rgb":case"rgba":if(r=/^\s*(\d+)\s*,\s*(\d+)\s*,\s*(\d+)\s*(?:,\s*(\d+(?:\.\d+)?|\.\d+)\s*)?$/.exec(o))return n(r[4]),this.setRGB(Math.min(255,parseInt(r[1],10))/255,Math.min(255,parseInt(r[2],10))/255,Math.min(255,parseInt(r[3],10))/255,t);if(r=/^\s*(\d+(?:\.\d+)?|\.\d+)\%\s*,\s*(\d+(?:\.\d+)?|\.\d+)\%\s*,\s*(\d+(?:\.\d+)?|\.\d+)\%\s*(?:,\s*(\d+(?:\.\d+)?|\.\d+)\s*)?$/.exec(o))return n(r[4]),this.setRGB(Math.min(100,parseFloat(r[1]))/100,Math.min(100,parseFloat(r[2]))/100,Math.min(100,parseFloat(r[3]))/100,t);break;case"hsl":case"hsla":if(r=/^\s*([+-]?(?:\d+(?:\.\d+)?|\.\d+))\s*,\s*(\d+(?:\.\d+)?|\.\d+)\%\s*,\s*(\d+(?:\.\d+)?|\.\d+)\%\s*(?:,\s*(\d+(?:\.\d+)?|\.\d+)\s*)?$/.exec(o))return n(r[4]),this.setHSL(parseFloat(r[1])/360,parseFloat(r[2])/100,parseFloat(r[3])/100,t);break;default:De("Color: Unknown color model "+e)}}else if(s=/^\#([A-Fa-f\d]+)$/.exec(e)){const r=s[1],a=r.length;if(a===3)return this.setRGB(parseInt(r.charAt(0),16)/15,parseInt(r.charAt(1),16)/15,parseInt(r.charAt(2),16)/15,t);if(a===6)return this.setHex(parseInt(r,16),t);De("Color: Invalid hex color "+e)}else if(e&&e.length>0)return this.setColorName(e,t);return this}setColorName(e,t=Jt){const n=Qo[e.toLowerCase()];return n!==void 0?this.setHex(n,t):De("Color: Unknown color "+e),this}clone(){return new this.constructor(this.r,this.g,this.b)}copy(e){return this.r=e.r,this.g=e.g,this.b=e.b,this}copySRGBToLinear(e){return this.r=An(e.r),this.g=An(e.g),this.b=An(e.b),this}copyLinearToSRGB(e){return this.r=gi(e.r),this.g=gi(e.g),this.b=gi(e.b),this}convertSRGBToLinear(){return this.copySRGBToLinear(this),this}convertLinearToSRGB(){return this.copyLinearToSRGB(this),this}getHex(e=Jt){return Ze.workingToColorSpace(Nt.copy(this),e),Math.round($e(Nt.r*255,0,255))*65536+Math.round($e(Nt.g*255,0,255))*256+Math.round($e(Nt.b*255,0,255))}getHexString(e=Jt){return("000000"+this.getHex(e).toString(16)).slice(-6)}getHSL(e,t=Ze.workingColorSpace){Ze.workingToColorSpace(Nt.copy(this),t);const n=Nt.r,s=Nt.g,r=Nt.b,a=Math.max(n,s,r),o=Math.min(n,s,r);let d,c;const m=(o+a)/2;if(o===a)d=0,c=0;else{const x=a-o;switch(c=m<=.5?x/(a+o):x/(2-a-o),a){case n:d=(s-r)/x+(s<r?6:0);break;case s:d=(r-n)/x+2;break;case r:d=(n-s)/x+4;break}d/=6}return e.h=d,e.s=c,e.l=m,e}getRGB(e,t=Ze.workingColorSpace){return Ze.workingToColorSpace(Nt.copy(this),t),e.r=Nt.r,e.g=Nt.g,e.b=Nt.b,e}getStyle(e=Jt){Ze.workingToColorSpace(Nt.copy(this),e);const t=Nt.r,n=Nt.g,s=Nt.b;return e!==Jt?`color(${e} ${t.toFixed(3)} ${n.toFixed(3)} ${s.toFixed(3)})`:`rgb(${Math.round(t*255)},${Math.round(n*255)},${Math.round(s*255)})`}offsetHSL(e,t,n){return this.getHSL(Ln),this.setHSL(Ln.h+e,Ln.s+t,Ln.l+n)}add(e){return this.r+=e.r,this.g+=e.g,this.b+=e.b,this}addColors(e,t){return this.r=e.r+t.r,this.g=e.g+t.g,this.b=e.b+t.b,this}addScalar(e){return this.r+=e,this.g+=e,this.b+=e,this}sub(e){return this.r=Math.max(0,this.r-e.r),this.g=Math.max(0,this.g-e.g),this.b=Math.max(0,this.b-e.b),this}multiply(e){return this.r*=e.r,this.g*=e.g,this.b*=e.b,this}multiplyScalar(e){return this.r*=e,this.g*=e,this.b*=e,this}lerp(e,t){return this.r+=(e.r-this.r)*t,this.g+=(e.g-this.g)*t,this.b+=(e.b-this.b)*t,this}lerpColors(e,t,n){return this.r=e.r+(t.r-e.r)*n,this.g=e.g+(t.g-e.g)*n,this.b=e.b+(t.b-e.b)*n,this}lerpHSL(e,t){this.getHSL(Ln),e.getHSL(qi);const n=ks(Ln.h,qi.h,t),s=ks(Ln.s,qi.s,t),r=ks(Ln.l,qi.l,t);return this.setHSL(n,s,r),this}setFromVector3(e){return this.r=e.x,this.g=e.y,this.b=e.z,this}applyMatrix3(e){const t=this.r,n=this.g,s=this.b,r=e.elements;return this.r=r[0]*t+r[3]*n+r[6]*s,this.g=r[1]*t+r[4]*n+r[7]*s,this.b=r[2]*t+r[5]*n+r[8]*s,this}equals(e){return e.r===this.r&&e.g===this.g&&e.b===this.b}fromArray(e,t=0){return this.r=e[t],this.g=e[t+1],this.b=e[t+2],this}toArray(e=[],t=0){return e[t]=this.r,e[t+1]=this.g,e[t+2]=this.b,e}fromBufferAttribute(e,t){return this.r=e.getX(t),this.g=e.getY(t),this.b=e.getZ(t),this}toJSON(){return this.getHex()}*[Symbol.iterator](){yield this.r,yield this.g,yield this.b}}const Nt=new je;je.NAMES=Qo;class Cc extends Ut{constructor(){super(),this.isScene=!0,this.type="Scene",this.background=null,this.environment=null,this.fog=null,this.backgroundBlurriness=0,this.backgroundIntensity=1,this.backgroundRotation=new Gn,this.environmentIntensity=1,this.environmentRotation=new Gn,this.overrideMaterial=null,typeof __THREE_DEVTOOLS__<"u"&&__THREE_DEVTOOLS__.dispatchEvent(new CustomEvent("observe",{detail:this}))}copy(e,t){return super.copy(e,t),this.background=e.background!==null?e.background.clone():null,this.environment=e.environment!==null?e.environment.clone():null,this.fog=e.fog!==null?e.fog.clone():null,this.backgroundBlurriness=e.backgroundBlurriness,this.backgroundIntensity=e.backgroundIntensity,this.backgroundRotation.copy(e.backgroundRotation),this.environmentIntensity=e.environmentIntensity,this.environmentRotation.copy(e.environmentRotation),this.overrideMaterial=e.overrideMaterial!==null?e.overrideMaterial.clone():null,this.matrixAutoUpdate=e.matrixAutoUpdate,this}toJSON(e){const t=super.toJSON(e);return this.fog!==null&&(t.object.fog=this.fog.toJSON()),t.object.backgroundBlurriness=this.backgroundBlurriness,t.object.backgroundIntensity=this.backgroundIntensity,t.object.backgroundRotation=this.backgroundRotation.toArray(),t.object.environmentIntensity=this.environmentIntensity,t.object.environmentRotation=this.environmentRotation.toArray(),t}}const nn=new z,Sn=new z,Ys=new z,Mn=new z,ai=new z,oi=new z,Ya=new z,Ks=new z,Zs=new z,$s=new z,Js=new pt,Qs=new pt,js=new pt;class on{constructor(e=new z,t=new z,n=new z){this.a=e,this.b=t,this.c=n}static getNormal(e,t,n,s){s.subVectors(n,t),nn.subVectors(e,t),s.cross(nn);const r=s.lengthSq();return r>0?s.multiplyScalar(1/Math.sqrt(r)):s.set(0,0,0)}static getBarycoord(e,t,n,s,r){nn.subVectors(s,t),Sn.subVectors(n,t),Ys.subVectors(e,t);const a=nn.dot(nn),o=nn.dot(Sn),d=nn.dot(Ys),c=Sn.dot(Sn),m=Sn.dot(Ys),x=a*c-o*o;if(x===0)return r.set(0,0,0),null;const f=1/x,g=(c*d-o*m)*f,l=(a*m-o*d)*f;return r.set(1-g-l,l,g)}static containsPoint(e,t,n,s){return this.getBarycoord(e,t,n,s,Mn)===null?!1:Mn.x>=0&&Mn.y>=0&&Mn.x+Mn.y<=1}static getInterpolation(e,t,n,s,r,a,o,d){return this.getBarycoord(e,t,n,s,Mn)===null?(d.x=0,d.y=0,"z"in d&&(d.z=0),"w"in d&&(d.w=0),null):(d.setScalar(0),d.addScaledVector(r,Mn.x),d.addScaledVector(a,Mn.y),d.addScaledVector(o,Mn.z),d)}static getInterpolatedAttribute(e,t,n,s,r,a){return Js.setScalar(0),Qs.setScalar(0),js.setScalar(0),Js.fromBufferAttribute(e,t),Qs.fromBufferAttribute(e,n),js.fromBufferAttribute(e,s),a.setScalar(0),a.addScaledVector(Js,r.x),a.addScaledVector(Qs,r.y),a.addScaledVector(js,r.z),a}static isFrontFacing(e,t,n,s){return nn.subVectors(n,t),Sn.subVectors(e,t),nn.cross(Sn).dot(s)<0}set(e,t,n){return this.a.copy(e),this.b.copy(t),this.c.copy(n),this}setFromPointsAndIndices(e,t,n,s){return this.a.copy(e[t]),this.b.copy(e[n]),this.c.copy(e[s]),this}setFromAttributeAndIndices(e,t,n,s){return this.a.fromBufferAttribute(e,t),this.b.fromBufferAttribute(e,n),this.c.fromBufferAttribute(e,s),this}clone(){return new this.constructor().copy(this)}copy(e){return this.a.copy(e.a),this.b.copy(e.b),this.c.copy(e.c),this}getArea(){return nn.subVectors(this.c,this.b),Sn.subVectors(this.a,this.b),nn.cross(Sn).length()*.5}getMidpoint(e){return e.addVectors(this.a,this.b).add(this.c).multiplyScalar(1/3)}getNormal(e){return on.getNormal(this.a,this.b,this.c,e)}getPlane(e){return e.setFromCoplanarPoints(this.a,this.b,this.c)}getBarycoord(e,t){return on.getBarycoord(e,this.a,this.b,this.c,t)}getInterpolation(e,t,n,s,r){return on.getInterpolation(e,this.a,this.b,this.c,t,n,s,r)}containsPoint(e){return on.containsPoint(e,this.a,this.b,this.c)}isFrontFacing(e){return on.isFrontFacing(this.a,this.b,this.c,e)}intersectsBox(e){return e.intersectsTriangle(this)}closestPointToPoint(e,t){const n=this.a,s=this.b,r=this.c;let a,o;ai.subVectors(s,n),oi.subVectors(r,n),Ks.subVectors(e,n);const d=ai.dot(Ks),c=oi.dot(Ks);if(d<=0&&c<=0)return t.copy(n);Zs.subVectors(e,s);const m=ai.dot(Zs),x=oi.dot(Zs);if(m>=0&&x<=m)return t.copy(s);const f=d*x-m*c;if(f<=0&&d>=0&&m<=0&&d-m>0)return a=d/(d-m),t.copy(n).addScaledVector(ai,a);$s.subVectors(e,r);const g=ai.dot($s),l=oi.dot($s);if(l>=0&&g<=l)return t.copy(r);const T=g*c-d*l;if(T<=0&&c>=0&&l<=0)return o=c/(c-l),t.copy(n).addScaledVector(oi,o);const p=m*l-g*x;if(p<=0&&x-m>=0&&g-l>=0)return Ya.subVectors(r,s),o=(x-m)/(x-m+(g-l)),t.copy(s).addScaledVector(Ya,o);const u=1/(p+T+f);return a=T*u,o=f*u,t.copy(n).addScaledVector(ai,a).addScaledVector(oi,o)}equals(e){return e.a.equals(this.a)&&e.b.equals(this.b)&&e.c.equals(this.c)}}class Bi{constructor(e=new z(1/0,1/0,1/0),t=new z(-1/0,-1/0,-1/0)){this.isBox3=!0,this.min=e,this.max=t}set(e,t){return this.min.copy(e),this.max.copy(t),this}setFromArray(e){this.makeEmpty();for(let t=0,n=e.length;t<n;t+=3)this.expandByPoint(sn.fromArray(e,t));return this}setFromBufferAttribute(e){this.makeEmpty();for(let t=0,n=e.count;t<n;t++)this.expandByPoint(sn.fromBufferAttribute(e,t));return this}setFromPoints(e){this.makeEmpty();for(let t=0,n=e.length;t<n;t++)this.expandByPoint(e[t]);return this}setFromCenterAndSize(e,t){const n=sn.copy(t).multiplyScalar(.5);return this.min.copy(e).sub(n),this.max.copy(e).add(n),this}setFromObject(e,t=!1){return this.makeEmpty(),this.expandByObject(e,t)}clone(){return new this.constructor().copy(this)}copy(e){return this.min.copy(e.min),this.max.copy(e.max),this}makeEmpty(){return this.min.x=this.min.y=this.min.z=1/0,this.max.x=this.max.y=this.max.z=-1/0,this}isEmpty(){return this.max.x<this.min.x||this.max.y<this.min.y||this.max.z<this.min.z}getCenter(e){return this.isEmpty()?e.set(0,0,0):e.addVectors(this.min,this.max).multiplyScalar(.5)}getSize(e){return this.isEmpty()?e.set(0,0,0):e.subVectors(this.max,this.min)}expandByPoint(e){return this.min.min(e),this.max.max(e),this}expandByVector(e){return this.min.sub(e),this.max.add(e),this}expandByScalar(e){return this.min.addScalar(-e),this.max.addScalar(e),this}expandByObject(e,t=!1){e.updateWorldMatrix(!1,!1);const n=e.geometry;if(n!==void 0){const r=n.getAttribute("position");if(t===!0&&r!==void 0&&e.isInstancedMesh!==!0&&e.isBatchedMesh!==!0)for(let a=0,o=r.count;a<o;a++)e.isMesh===!0?e.getVertexPosition(a,sn):sn.fromBufferAttribute(r,a),sn.applyMatrix4(e.matrixWorld),this.expandByPoint(sn);else e.boundingBox!==void 0?(e.boundingBox===null&&e.computeBoundingBox(),Yi.copy(e.boundingBox)):(n.boundingBox===null&&n.computeBoundingBox(),Yi.copy(n.boundingBox)),Yi.applyMatrix4(e.matrixWorld),this.union(Yi)}const s=e.children;for(let r=0,a=s.length;r<a;r++)this.expandByObject(s[r],t);return this}containsPoint(e){return e.x>=this.min.x&&e.x<=this.max.x&&e.y>=this.min.y&&e.y<=this.max.y&&e.z>=this.min.z&&e.z<=this.max.z}containsBox(e){return this.min.x<=e.min.x&&e.max.x<=this.max.x&&this.min.y<=e.min.y&&e.max.y<=this.max.y&&this.min.z<=e.min.z&&e.max.z<=this.max.z}getParameter(e,t){return t.set((e.x-this.min.x)/(this.max.x-this.min.x),(e.y-this.min.y)/(this.max.y-this.min.y),(e.z-this.min.z)/(this.max.z-this.min.z))}intersectsBox(e){return e.max.x>=this.min.x&&e.min.x<=this.max.x&&e.max.y>=this.min.y&&e.min.y<=this.max.y&&e.max.z>=this.min.z&&e.min.z<=this.max.z}intersectsSphere(e){return this.clampPoint(e.center,sn),sn.distanceToSquared(e.center)<=e.radius*e.radius}intersectsPlane(e){let t,n;return e.normal.x>0?(t=e.normal.x*this.min.x,n=e.normal.x*this.max.x):(t=e.normal.x*this.max.x,n=e.normal.x*this.min.x),e.normal.y>0?(t+=e.normal.y*this.min.y,n+=e.normal.y*this.max.y):(t+=e.normal.y*this.max.y,n+=e.normal.y*this.min.y),e.normal.z>0?(t+=e.normal.z*this.min.z,n+=e.normal.z*this.max.z):(t+=e.normal.z*this.max.z,n+=e.normal.z*this.min.z),t<=-e.constant&&n>=-e.constant}intersectsTriangle(e){if(this.isEmpty())return!1;this.getCenter(Ti),Ki.subVectors(this.max,Ti),li.subVectors(e.a,Ti),ci.subVectors(e.b,Ti),di.subVectors(e.c,Ti),Dn.subVectors(ci,li),In.subVectors(di,ci),qn.subVectors(li,di);let t=[0,-Dn.z,Dn.y,0,-In.z,In.y,0,-qn.z,qn.y,Dn.z,0,-Dn.x,In.z,0,-In.x,qn.z,0,-qn.x,-Dn.y,Dn.x,0,-In.y,In.x,0,-qn.y,qn.x,0];return!er(t,li,ci,di,Ki)||(t=[1,0,0,0,1,0,0,0,1],!er(t,li,ci,di,Ki))?!1:(Zi.crossVectors(Dn,In),t=[Zi.x,Zi.y,Zi.z],er(t,li,ci,di,Ki))}clampPoint(e,t){return t.copy(e).clamp(this.min,this.max)}distanceToPoint(e){return this.clampPoint(e,sn).distanceTo(e)}getBoundingSphere(e){return this.isEmpty()?e.makeEmpty():(this.getCenter(e.center),e.radius=this.getSize(sn).length()*.5),e}intersect(e){return this.min.max(e.min),this.max.min(e.max),this.isEmpty()&&this.makeEmpty(),this}union(e){return this.min.min(e.min),this.max.max(e.max),this}applyMatrix4(e){return this.isEmpty()?this:(bn[0].set(this.min.x,this.min.y,this.min.z).applyMatrix4(e),bn[1].set(this.min.x,this.min.y,this.max.z).applyMatrix4(e),bn[2].set(this.min.x,this.max.y,this.min.z).applyMatrix4(e),bn[3].set(this.min.x,this.max.y,this.max.z).applyMatrix4(e),bn[4].set(this.max.x,this.min.y,this.min.z).applyMatrix4(e),bn[5].set(this.max.x,this.min.y,this.max.z).applyMatrix4(e),bn[6].set(this.max.x,this.max.y,this.min.z).applyMatrix4(e),bn[7].set(this.max.x,this.max.y,this.max.z).applyMatrix4(e),this.setFromPoints(bn),this)}translate(e){return this.min.add(e),this.max.add(e),this}equals(e){return e.min.equals(this.min)&&e.max.equals(this.max)}toJSON(){return{min:this.min.toArray(),max:this.max.toArray()}}fromJSON(e){return this.min.fromArray(e.min),this.max.fromArray(e.max),this}}const bn=[new z,new z,new z,new z,new z,new z,new z,new z],sn=new z,Yi=new Bi,li=new z,ci=new z,di=new z,Dn=new z,In=new z,qn=new z,Ti=new z,Ki=new z,Zi=new z,Yn=new z;function er(i,e,t,n,s){for(let r=0,a=i.length-3;r<=a;r+=3){Yn.fromArray(i,r);const o=s.x*Math.abs(Yn.x)+s.y*Math.abs(Yn.y)+s.z*Math.abs(Yn.z),d=e.dot(Yn),c=t.dot(Yn),m=n.dot(Yn);if(Math.max(-Math.max(d,c,m),Math.min(d,c,m))>o)return!1}return!0}const Mt=new z,$i=new Xe;let Pc=0;class $n extends ei{constructor(e,t,n=!1){if(super(),Array.isArray(e))throw new TypeError("THREE.BufferAttribute: array should be a Typed Array.");this.isBufferAttribute=!0,Object.defineProperty(this,"id",{value:Pc++}),this.name="",this.array=e,this.itemSize=t,this.count=e!==void 0?e.length/t:0,this.normalized=n,this.usage=oc,this.updateRanges=[],this.gpuType=hn,this.version=0,this._normalizationScale=e!==void 0?aa(e):void 0}onUploadCallback(){}set needsUpdate(e){e===!0&&this.version++}setUsage(e){return this.usage=e,this}addUpdateRange(e,t){this.updateRanges.push({start:e,count:t})}clearUpdateRanges(){this.updateRanges.length=0}copy(e){return this.name=e.name,this.array=new e.array.constructor(e.array),this.itemSize=e.itemSize,this.count=e.count,this.normalized=e.normalized,this.usage=e.usage,this.gpuType=e.gpuType,this._normalizationScale=e._normalizationScale,this}copyAt(e,t,n){e*=this.itemSize,n*=t.itemSize;for(let s=0,r=this.itemSize;s<r;s++)this.array[e+s]=t.array[n+s];return this}copyArray(e){return this.array.set(e),this}applyMatrix3(e){if(this.itemSize===2)for(let t=0,n=this.count;t<n;t++)$i.fromBufferAttribute(this,t),$i.applyMatrix3(e),this.setXY(t,$i.x,$i.y);else if(this.itemSize===3)for(let t=0,n=this.count;t<n;t++)Mt.fromBufferAttribute(this,t),Mt.applyMatrix3(e),this.setXYZ(t,Mt.x,Mt.y,Mt.z);return this}applyMatrix4(e){for(let t=0,n=this.count;t<n;t++)Mt.fromBufferAttribute(this,t),Mt.applyMatrix4(e),this.setXYZ(t,Mt.x,Mt.y,Mt.z);return this}applyNormalMatrix(e){for(let t=0,n=this.count;t<n;t++)Mt.fromBufferAttribute(this,t),Mt.applyNormalMatrix(e),this.setXYZ(t,Mt.x,Mt.y,Mt.z);return this}transformDirection(e){for(let t=0,n=this.count;t<n;t++)Mt.fromBufferAttribute(this,t),Mt.transformDirection(e),this.setXYZ(t,Mt.x,Mt.y,Mt.z);return this}set(e,t=0){return this.array.set(e,t),this}getComponent(e,t){let n=this.array[e*this.itemSize+t];return this.normalized&&(n=this._denormalize(n)),n}setComponent(e,t,n){return this.normalized&&(n=this._normalize(n)),this.array[e*this.itemSize+t]=n,this}getX(e){let t=this.array[e*this.itemSize];return this.normalized&&(t=this._denormalize(t)),t}setX(e,t){return this.normalized&&(t=this._normalize(t)),this.array[e*this.itemSize]=t,this}getY(e){let t=this.array[e*this.itemSize+1];return this.normalized&&(t=this._denormalize(t)),t}setY(e,t){return this.normalized&&(t=this._normalize(t)),this.array[e*this.itemSize+1]=t,this}getZ(e){let t=this.array[e*this.itemSize+2];return this.normalized&&(t=this._denormalize(t)),t}setZ(e,t){return this.normalized&&(t=this._normalize(t)),this.array[e*this.itemSize+2]=t,this}getW(e){let t=this.array[e*this.itemSize+3];return this.normalized&&(t=this._denormalize(t)),t}setW(e,t){return this.normalized&&(t=this._normalize(t)),this.array[e*this.itemSize+3]=t,this}setXY(e,t,n){return e*=this.itemSize,this.normalized&&(t=this._normalize(t),n=this._normalize(n)),this.array[e+0]=t,this.array[e+1]=n,this}setXYZ(e,t,n,s){return e*=this.itemSize,this.normalized&&(t=this._normalize(t),n=this._normalize(n),s=this._normalize(s)),this.array[e+0]=t,this.array[e+1]=n,this.array[e+2]=s,this}setXYZW(e,t,n,s,r){return e*=this.itemSize,this.normalized&&(t=this._normalize(t),n=this._normalize(n),s=this._normalize(s),r=this._normalize(r)),this.array[e+0]=t,this.array[e+1]=n,this.array[e+2]=s,this.array[e+3]=r,this}onUpload(e){return this.onUploadCallback=e,this}clone(){return new this.constructor(this.array,this.itemSize).copy(this)}toJSON(){const e={itemSize:this.itemSize,type:this.array.constructor.name,array:Array.from(this.array),normalized:this.normalized};return e.name=this.name,e.usage=this.usage,e.gpuType=this.gpuType,e}dispose(){this.dispatchEvent({type:"dispose"})}_denormalize(e){const t=this._normalizationScale;return t!==void 0?Math.max(e/t,-1):pc(e,this.array)}_normalize(e){const t=this._normalizationScale;return t!==void 0?Math.round(e*t):mc(e,this.array)}}class jo extends $n{constructor(e,t,n){super(new Uint16Array(e),t,n)}}class el extends $n{constructor(e,t,n){super(new Uint32Array(e),t,n)}}class _t extends $n{constructor(e,t,n){super(new Float32Array(e),t,n)}}const Lc=new Bi,wi=new z,tr=new z;class la{constructor(e=new z,t=-1){this.isSphere=!0,this.center=e,this.radius=t}set(e,t){return this.center.copy(e),this.radius=t,this}setFromPoints(e,t){const n=this.center;t!==void 0?n.copy(t):Lc.setFromPoints(e).getCenter(n);let s=0;for(let r=0,a=e.length;r<a;r++)s=Math.max(s,n.distanceToSquared(e[r]));return this.radius=Math.sqrt(s),this}copy(e){return this.center.copy(e.center),this.radius=e.radius,this}isEmpty(){return this.radius<0}makeEmpty(){return this.center.set(0,0,0),this.radius=-1,this}containsPoint(e){return e.distanceToSquared(this.center)<=this.radius*this.radius}distanceToPoint(e){return e.distanceTo(this.center)-this.radius}intersectsSphere(e){const t=this.radius+e.radius;return e.center.distanceToSquared(this.center)<=t*t}intersectsBox(e){return e.intersectsSphere(this)}intersectsPlane(e){return Math.abs(e.distanceToPoint(this.center))<=this.radius}clampPoint(e,t){const n=this.center.distanceToSquared(e);return t.copy(e),n>this.radius*this.radius&&(t.sub(this.center).normalize(),t.multiplyScalar(this.radius).add(this.center)),t}getBoundingBox(e){return this.isEmpty()?(e.makeEmpty(),e):(e.set(this.center,this.center),e.expandByScalar(this.radius),e)}applyMatrix4(e){return this.center.applyMatrix4(e),this.radius=this.radius*e.getMaxScaleOnAxis(),this}translate(e){return this.center.add(e),this}expandByPoint(e){if(this.isEmpty())return this.center.copy(e),this.radius=0,this;wi.subVectors(e,this.center);const t=wi.lengthSq();if(t>this.radius*this.radius){const n=Math.sqrt(t),s=(n-this.radius)*.5;this.center.addScaledVector(wi,s/n),this.radius+=s}return this}union(e){return e.isEmpty()?this:this.isEmpty()?(this.copy(e),this):(this.center.equals(e.center)===!0?this.radius=Math.max(this.radius,e.radius):(tr.subVectors(e.center,this.center).setLength(e.radius),this.expandByPoint(wi.copy(e.center).add(tr)),this.expandByPoint(wi.copy(e.center).sub(tr))),this)}equals(e){return e.center.equals(this.center)&&e.radius===this.radius}clone(){return new this.constructor().copy(this)}toJSON(){return{radius:this.radius,center:this.center.toArray()}}fromJSON(e){return this.radius=e.radius,this.center.fromArray(e.center),this}}let Dc=0;const $t=new mt,nr=new Ut,ui=new z,Kt=new Bi,Ai=new Bi,At=new z;class cn extends ei{constructor(){super(),this.isBufferGeometry=!0,Object.defineProperty(this,"id",{value:Dc++}),this.uuid=Oi(),this.name="",this.type="BufferGeometry",this.index=null,this.indirect=null,this.indirectOffset=0,this.attributes={},this.morphAttributes={},this.morphTargetsRelative=!1,this.groups=[],this.boundingBox=null,this.boundingSphere=null,this.drawRange={start:0,count:1/0},this.userData={},this._transformed=!1}getIndex(){return this.index}setIndex(e){return Array.isArray(e)?this.index=new(lc(e)?el:jo)(e,1):this.index=e,this}setIndirect(e,t=0){return this.indirect=e,this.indirectOffset=t,this}getIndirect(){return this.indirect}getAttribute(e){return this.attributes[e]}setAttribute(e,t){return this.attributes[e]=t,this}deleteAttribute(e){return delete this.attributes[e],this}hasAttribute(e){return this.attributes[e]!==void 0}addGroup(e,t,n=0){this.groups.push({start:e,count:t,materialIndex:n})}clearGroups(){this.groups=[]}setDrawRange(e,t){this.drawRange.start=e,this.drawRange.count=t}applyMatrix4(e){const t=this.attributes.position;t!==void 0&&(t.applyMatrix4(e),t.needsUpdate=!0);const n=this.attributes.normal;if(n!==void 0){const r=new Ne().getNormalMatrix(e);n.applyNormalMatrix(r),n.needsUpdate=!0}const s=this.attributes.tangent;return s!==void 0&&(s.transformDirection(e),s.needsUpdate=!0),this.boundingBox!==null&&this.computeBoundingBox(),this.boundingSphere!==null&&this.computeBoundingSphere(),this._transformed=!0,this}applyQuaternion(e){return $t.makeRotationFromQuaternion(e),this.applyMatrix4($t),this}rotateX(e){return $t.makeRotationX(e),this.applyMatrix4($t),this}rotateY(e){return $t.makeRotationY(e),this.applyMatrix4($t),this}rotateZ(e){return $t.makeRotationZ(e),this.applyMatrix4($t),this}translate(e,t,n){return $t.makeTranslation(e,t,n),this.applyMatrix4($t),this}scale(e,t,n){return $t.makeScale(e,t,n),this.applyMatrix4($t),this}lookAt(e){return nr.lookAt(e),nr.updateMatrix(),this.applyMatrix4(nr.matrix),this}center(){return this.computeBoundingBox(),this.boundingBox.getCenter(ui).negate(),this.translate(ui.x,ui.y,ui.z),this}setFromPoints(e){const t=this.getAttribute("position");if(t===void 0){const n=[];for(let s=0,r=e.length;s<r;s++){const a=e[s];n.push(a.x,a.y,a.z||0)}this.setAttribute("position",new _t(n,3))}else{const n=Math.min(e.length,t.count);for(let s=0;s<n;s++){const r=e[s];t.setXYZ(s,r.x,r.y,r.z||0)}e.length>t.count&&De("BufferGeometry: Buffer size too small for points data. Use .dispose() and create a new geometry."),t.needsUpdate=!0}return this}computeBoundingBox(){this.boundingBox===null&&(this.boundingBox=new Bi);const e=this.attributes.position,t=this.morphAttributes.position;if(e&&e.isGLBufferAttribute){nt("BufferGeometry.computeBoundingBox(): GLBufferAttribute requires a manual bounding box.",this),this.boundingBox.set(new z(-1/0,-1/0,-1/0),new z(1/0,1/0,1/0));return}if(e!==void 0){if(this.boundingBox.setFromBufferAttribute(e),t)for(let n=0,s=t.length;n<s;n++){const r=t[n];Kt.setFromBufferAttribute(r),this.morphTargetsRelative?(At.addVectors(this.boundingBox.min,Kt.min),this.boundingBox.expandByPoint(At),At.addVectors(this.boundingBox.max,Kt.max),this.boundingBox.expandByPoint(At)):(this.boundingBox.expandByPoint(Kt.min),this.boundingBox.expandByPoint(Kt.max))}}else this.boundingBox.makeEmpty();(isNaN(this.boundingBox.min.x)||isNaN(this.boundingBox.min.y)||isNaN(this.boundingBox.min.z))&&nt('BufferGeometry.computeBoundingBox(): Computed min/max have NaN values. The "position" attribute is likely to have NaN values.',this)}computeBoundingSphere(){this.boundingSphere===null&&(this.boundingSphere=new la);const e=this.attributes.position,t=this.morphAttributes.position;if(e&&e.isGLBufferAttribute){nt("BufferGeometry.computeBoundingSphere(): GLBufferAttribute requires a manual bounding sphere.",this),this.boundingSphere.set(new z,1/0);return}if(e){const n=this.boundingSphere.center;if(Kt.setFromBufferAttribute(e),t)for(let r=0,a=t.length;r<a;r++){const o=t[r];Ai.setFromBufferAttribute(o),this.morphTargetsRelative?(At.addVectors(Kt.min,Ai.min),Kt.expandByPoint(At),At.addVectors(Kt.max,Ai.max),Kt.expandByPoint(At)):(Kt.expandByPoint(Ai.min),Kt.expandByPoint(Ai.max))}Kt.getCenter(n);let s=0;for(let r=0,a=e.count;r<a;r++)At.fromBufferAttribute(e,r),s=Math.max(s,n.distanceToSquared(At));if(t)for(let r=0,a=t.length;r<a;r++){const o=t[r],d=this.morphTargetsRelative;for(let c=0,m=o.count;c<m;c++)At.fromBufferAttribute(o,c),d&&(ui.fromBufferAttribute(e,c),At.add(ui)),s=Math.max(s,n.distanceToSquared(At))}this.boundingSphere.radius=Math.sqrt(s),isNaN(this.boundingSphere.radius)&&nt('BufferGeometry.computeBoundingSphere(): Computed radius is NaN. The "position" attribute is likely to have NaN values.',this)}}computeTangents(){const e=this.index,t=this.attributes;if(e===null||t.position===void 0||t.normal===void 0||t.uv===void 0){nt("BufferGeometry: .computeTangents() failed. Missing required attributes (index, position, normal or uv)");return}const n=t.position,s=t.normal,r=t.uv;let a=this.getAttribute("tangent");(a===void 0||a.count!==n.count)&&(a=new $n(new Float32Array(4*n.count),4),this.setAttribute("tangent",a));const o=[],d=[],c=new Uint8Array(n.count);for(let I=0;I<n.count;I++)o[I]=new z,d[I]=new z;const m=new z,x=new z,f=new z,g=new Xe,l=new Xe,T=new Xe,p=new z,u=new z;function b(I,C,U){c[I]=c[C]=c[U]=1,m.fromBufferAttribute(n,I),x.fromBufferAttribute(n,C),f.fromBufferAttribute(n,U),g.fromBufferAttribute(r,I),l.fromBufferAttribute(r,C),T.fromBufferAttribute(r,U),x.sub(m),f.sub(m),l.sub(g),T.sub(g);const F=1/(l.x*T.y-T.x*l.y);isFinite(F)&&(p.copy(x).multiplyScalar(T.y).addScaledVector(f,-l.y).multiplyScalar(F),u.copy(f).multiplyScalar(l.x).addScaledVector(x,-T.x).multiplyScalar(F),o[I].add(p),o[C].add(p),o[U].add(p),d[I].add(u),d[C].add(u),d[U].add(u))}let y=this.groups;y.length===0&&(y=[{start:0,count:e.count}]);for(let I=0,C=y.length;I<C;++I){const U=y[I],F=U.start,X=U.count;for(let L=F,W=F+X;L<W;L+=3)b(e.getX(L+0),e.getX(L+1),e.getX(L+2))}const E=new z,R=new z,A=new z,h=new z;function M(I){A.fromBufferAttribute(s,I),h.copy(A);const C=o[I];E.copy(C),E.sub(A.multiplyScalar(A.dot(C))).normalize(),R.crossVectors(h,C);const F=R.dot(d[I])<0?-1:1;a.setXYZW(I,E.x,E.y,E.z,F)}for(let I=0,C=n.count;I<C;++I)c[I]&&M(I);this._transformed=!0}computeVertexNormals(){const e=this.index,t=this.getAttribute("position");if(t!==void 0){let n=this.getAttribute("normal");if(n===void 0||n.count!==t.count)n=new $n(new Float32Array(t.count*3),3),this.setAttribute("normal",n);else for(let f=0,g=n.count;f<g;f++)n.setXYZ(f,0,0,0);const s=new z,r=new z,a=new z,o=new z,d=new z,c=new z,m=new z,x=new z;if(e)for(let f=0,g=e.count;f<g;f+=3){const l=e.getX(f+0),T=e.getX(f+1),p=e.getX(f+2);s.fromBufferAttribute(t,l),r.fromBufferAttribute(t,T),a.fromBufferAttribute(t,p),m.subVectors(a,r),x.subVectors(s,r),m.cross(x),o.fromBufferAttribute(n,l),d.fromBufferAttribute(n,T),c.fromBufferAttribute(n,p),o.add(m),d.add(m),c.add(m),n.setXYZ(l,o.x,o.y,o.z),n.setXYZ(T,d.x,d.y,d.z),n.setXYZ(p,c.x,c.y,c.z)}else for(let f=0,g=t.count;f<g;f+=3)s.fromBufferAttribute(t,f+0),r.fromBufferAttribute(t,f+1),a.fromBufferAttribute(t,f+2),m.subVectors(a,r),x.subVectors(s,r),m.cross(x),n.setXYZ(f+0,m.x,m.y,m.z),n.setXYZ(f+1,m.x,m.y,m.z),n.setXYZ(f+2,m.x,m.y,m.z);this.normalizeNormals(),n.needsUpdate=!0}}normalizeNormals(){const e=this.attributes.normal;for(let t=0,n=e.count;t<n;t++)At.fromBufferAttribute(e,t),At.normalize(),e.setXYZ(t,At.x,At.y,At.z)}toNonIndexed(){function e(o,d){const c=o.array,m=o.itemSize,x=o.normalized,f=new c.constructor(d.length*m);let g=0,l=0;for(let T=0,p=d.length;T<p;T++){o.isInterleavedBufferAttribute?g=d[T]*o.data.stride+o.offset:g=d[T]*m;for(let u=0;u<m;u++)f[l++]=c[g++]}return new $n(f,m,x)}if(this.index===null)return De("BufferGeometry.toNonIndexed(): BufferGeometry is already non-indexed."),this;const t=new cn,n=this.index.array,s=this.attributes;for(const o in s){const d=s[o],c=e(d,n);t.setAttribute(o,c)}const r=this.morphAttributes;for(const o in r){const d=[],c=r[o];for(let m=0,x=c.length;m<x;m++){const f=c[m],g=e(f,n);d.push(g)}t.morphAttributes[o]=d}t.morphTargetsRelative=this.morphTargetsRelative;const a=this.groups;for(let o=0,d=a.length;o<d;o++){const c=a[o];t.addGroup(c.start,c.count,c.materialIndex)}return t}toJSON(){const e={metadata:{version:4.7,type:"BufferGeometry",generator:"BufferGeometry.toJSON"}};if(e.uuid=this.uuid,e.type=this.parameters!==void 0&&this._transformed===!0?"BufferGeometry":this.type,e.name=this.name,Object.keys(this.userData).length>0&&(e.userData=this.userData),this.parameters!==void 0&&this._transformed!==!0){const d=this.parameters;for(const c in d)d[c]!==void 0&&(e[c]=d[c]);return e}e.data={attributes:{}};const t=this.index;t!==null&&(e.data.index={type:t.array.constructor.name,array:Array.prototype.slice.call(t.array)});const n=this.attributes;for(const d in n){const c=n[d];e.data.attributes[d]=c.toJSON(e.data)}const s={};let r=!1;for(const d in this.morphAttributes){const c=this.morphAttributes[d],m=[];for(let x=0,f=c.length;x<f;x++){const g=c[x];m.push(g.toJSON(e.data))}m.length>0&&(s[d]=m,r=!0)}r&&(e.data.morphAttributes=s,e.data.morphTargetsRelative=this.morphTargetsRelative);const a=this.groups;a.length>0&&(e.data.groups=JSON.parse(JSON.stringify(a)));const o=this.boundingSphere;return o!==null&&(e.data.boundingSphere=o.toJSON()),e}clone(){return new this.constructor().copy(this)}copy(e){this.index=null,this.attributes={},this.morphAttributes={},this.groups=[],this.boundingBox=null,this.boundingSphere=null;const t={};this.name=e.name;const n=e.index;n!==null&&this.setIndex(n.clone());const s=e.attributes;for(const c in s){const m=s[c];this.setAttribute(c,m.clone(t))}const r=e.morphAttributes;for(const c in r){const m=[],x=r[c];for(let f=0,g=x.length;f<g;f++)m.push(x[f].clone(t));this.morphAttributes[c]=m}this.morphTargetsRelative=e.morphTargetsRelative;const a=e.groups;for(let c=0,m=a.length;c<m;c++){const x=a[c];this.addGroup(x.start,x.count,x.materialIndex)}const o=e.boundingBox;o!==null&&(this.boundingBox=o.clone());const d=e.boundingSphere;return d!==null&&(this.boundingSphere=d.clone()),this.drawRange.start=e.drawRange.start,this.drawRange.count=e.drawRange.count,this.userData=e.userData,this._transformed=e._transformed,this}dispose(){this.dispatchEvent({type:"dispose"})}}const ir=new z,Ic=new z,Nc=new Ne;class Un{constructor(e=new z(1,0,0),t=0){this.isPlane=!0,this.normal=e,this.constant=t}set(e,t){return this.normal.copy(e),this.constant=t,this}setComponents(e,t,n,s){return this.normal.set(e,t,n),this.constant=s,this}setFromNormalAndCoplanarPoint(e,t){return this.normal.copy(e),this.constant=-t.dot(this.normal),this}setFromCoplanarPoints(e,t,n){const s=ir.subVectors(n,t).cross(Ic.subVectors(e,t)).normalize();return this.setFromNormalAndCoplanarPoint(s,e),this}copy(e){return this.normal.copy(e.normal),this.constant=e.constant,this}normalize(){const e=1/this.normal.length();return this.normal.multiplyScalar(e),this.constant*=e,this}negate(){return this.constant*=-1,this.normal.negate(),this}distanceToPoint(e){return this.normal.dot(e)+this.constant}distanceToSphere(e){return this.distanceToPoint(e.center)-e.radius}projectPoint(e,t){return t.copy(e).addScaledVector(this.normal,-this.distanceToPoint(e))}intersectLine(e,t,n=!0){const s=e.delta(ir),r=this.normal.dot(s);if(r===0)return this.distanceToPoint(e.start)===0?t.copy(e.start):null;const a=-(e.start.dot(this.normal)+this.constant)/r;return n===!0&&(a<0||a>1)?null:t.copy(e.start).addScaledVector(s,a)}intersectsLine(e){const t=this.distanceToPoint(e.start),n=this.distanceToPoint(e.end);return t<0&&n>0||n<0&&t>0}intersectsBox(e){return e.intersectsPlane(this)}intersectsSphere(e){return e.intersectsPlane(this)}coplanarPoint(e){return e.copy(this.normal).multiplyScalar(-this.constant)}applyMatrix4(e,t){const n=t||Nc.getNormalMatrix(e),s=this.coplanarPoint(ir).applyMatrix4(e),r=this.normal.applyMatrix3(n).normalize();return this.constant=-s.dot(r),this}translate(e){return this.constant-=e.dot(this.normal),this}equals(e){return e.normal.equals(this.normal)&&e.constant===this.constant}clone(){return new this.constructor().copy(this)}toJSON(){return{normal:this.normal.toArray(),constant:this.constant}}fromJSON(e){return this.normal.fromArray(e.normal),this.constant=e.constant,this}}let Uc=0;class ki extends ei{constructor(){super(),this.isMaterial=!0,Object.defineProperty(this,"id",{value:Uc++}),this.uuid=Oi(),this.name="",this.type="Material",this.blending=Li,this.side=Jn,this.vertexColors=!1,this.opacity=1,this.transparent=!1,this.alphaHash=!1,this.blendSrc=Co,this.blendDst=Po,this.blendEquation=pi,this.blendSrcAlpha=null,this.blendDstAlpha=null,this.blendEquationAlpha=null,this.blendColor=new je(0,0,0),this.blendAlpha=0,this.depthFunc=Di,this.depthTest=!0,this.depthWrite=!0,this.stencilWriteMask=255,this.stencilFunc=tc,this.stencilRef=0,this.stencilFuncMask=255,this.stencilFail=Os,this.stencilZFail=Os,this.stencilZPass=Os,this.stencilWrite=!1,this.clippingPlanes=null,this.clipIntersection=!1,this.clipShadows=!1,this.shadowSide=null,this.colorWrite=!0,this.precision=null,this.polygonOffset=!1,this.polygonOffsetFactor=0,this.polygonOffsetUnits=0,this.dithering=!1,this.alphaToCoverage=!1,this.premultipliedAlpha=!1,this.forceSinglePass=!1,this.allowOverride=!0,this.visible=!0,this.toneMapped=!0,this.userData={},this.version=0,this._alphaTest=0}get alphaTest(){return this._alphaTest}set alphaTest(e){this._alphaTest>0!=e>0&&this.version++,this._alphaTest=e}onBeforeRender(){}onBeforeCompile(){}customProgramCacheKey(){return this.onBeforeCompile.toString()}setValues(e){if(e!==void 0)for(const t in e){const n=e[t];if(n===void 0){De(`Material: parameter '${t}' has value of undefined.`);continue}const s=this[t];if(s===void 0){De(`Material: '${t}' is not a property of THREE.${this.type}.`);continue}s&&s.isColor?s.set(n):s&&s.isVector2&&n&&n.isVector2||s&&s.isEuler&&n&&n.isEuler||s&&s.isVector3&&n&&n.isVector3?s.copy(n):this[t]=n}}toJSON(e){const t=e===void 0||typeof e=="string";t&&(e={textures:{},images:{}});const n={metadata:{version:4.7,type:"Material",generator:"Material.toJSON"}};n.uuid=this.uuid,n.type=this.type,n.blending=this.blending,n.side=this.side,n.shadowSide=this.shadowSide,n.vertexColors=this.vertexColors,n.opacity=this.opacity,n.transparent=this.transparent,n.blendSrc=this.blendSrc,n.blendDst=this.blendDst,n.blendEquation=this.blendEquation,n.blendSrcAlpha=this.blendSrcAlpha,n.blendDstAlpha=this.blendDstAlpha,n.blendEquationAlpha=this.blendEquationAlpha,n.blendColor=this.blendColor.getHex(),n.blendAlpha=this.blendAlpha,n.depthFunc=this.depthFunc,n.depthTest=this.depthTest,n.depthWrite=this.depthWrite,n.colorWrite=this.colorWrite,n.clipIntersection=this.clipIntersection,n.clipShadows=this.clipShadows,n.stencilWriteMask=this.stencilWriteMask,n.stencilFunc=this.stencilFunc,n.stencilRef=this.stencilRef,n.stencilFuncMask=this.stencilFuncMask,n.stencilFail=this.stencilFail,n.stencilZFail=this.stencilZFail,n.stencilZPass=this.stencilZPass,n.stencilWrite=this.stencilWrite,n.polygonOffset=this.polygonOffset,n.polygonOffsetFactor=this.polygonOffsetFactor,n.polygonOffsetUnits=this.polygonOffsetUnits,n.dithering=this.dithering,n.alphaTest=this.alphaTest,n.alphaHash=this.alphaHash,n.alphaToCoverage=this.alphaToCoverage,n.premultipliedAlpha=this.premultipliedAlpha,n.forceSinglePass=this.forceSinglePass,n.allowOverride=this.allowOverride,n.visible=this.visible,n.toneMapped=this.toneMapped,n.name=this.name,this.color&&this.color.isColor&&(n.color=this.color.getHex()),this.roughness!==void 0&&(n.roughness=this.roughness),this.metalness!==void 0&&(n.metalness=this.metalness),this.sheen!==void 0&&(n.sheen=this.sheen),this.sheenColor&&this.sheenColor.isColor&&(n.sheenColor=this.sheenColor.getHex()),this.sheenRoughness!==void 0&&(n.sheenRoughness=this.sheenRoughness),this.emissive&&this.emissive.isColor&&(n.emissive=this.emissive.getHex()),this.emissiveIntensity!==void 0&&(n.emissiveIntensity=this.emissiveIntensity),this.specular&&this.specular.isColor&&(n.specular=this.specular.getHex()),this.specularIntensity!==void 0&&(n.specularIntensity=this.specularIntensity),this.specularColor&&this.specularColor.isColor&&(n.specularColor=this.specularColor.getHex()),this.shininess!==void 0&&(n.shininess=this.shininess),this.clearcoat!==void 0&&(n.clearcoat=this.clearcoat),this.clearcoatRoughness!==void 0&&(n.clearcoatRoughness=this.clearcoatRoughness),this.diffuseRoughness!==void 0&&(n.diffuseRoughness=this.diffuseRoughness),this.diffuseRoughnessMap&&this.diffuseRoughnessMap.isTexture&&(n.diffuseRoughnessMap=this.diffuseRoughnessMap.toJSON(e).uuid),this.clearcoatMap&&this.clearcoatMap.isTexture&&(n.clearcoatMap=this.clearcoatMap.toJSON(e).uuid),this.clearcoatRoughnessMap&&this.clearcoatRoughnessMap.isTexture&&(n.clearcoatRoughnessMap=this.clearcoatRoughnessMap.toJSON(e).uuid),this.clearcoatNormalMap&&this.clearcoatNormalMap.isTexture&&(n.clearcoatNormalMap=this.clearcoatNormalMap.toJSON(e).uuid,n.clearcoatNormalScale=this.clearcoatNormalScale.toArray()),this.sheenColorMap&&this.sheenColorMap.isTexture&&(n.sheenColorMap=this.sheenColorMap.toJSON(e).uuid),this.sheenRoughnessMap&&this.sheenRoughnessMap.isTexture&&(n.sheenRoughnessMap=this.sheenRoughnessMap.toJSON(e).uuid),this.dispersion!==void 0&&(n.dispersion=this.dispersion),this.retroreflectivity!==void 0&&(n.retroreflectivity=this.retroreflectivity),this.iridescence!==void 0&&(n.iridescence=this.iridescence),this.iridescenceIOR!==void 0&&(n.iridescenceIOR=this.iridescenceIOR),this.iridescenceThicknessMinimum!==void 0&&(n.iridescenceThicknessMinimum=this.iridescenceThicknessMinimum),this.iridescenceThicknessMaximum!==void 0&&(n.iridescenceThicknessMaximum=this.iridescenceThicknessMaximum),this.iridescenceMap&&this.iridescenceMap.isTexture&&(n.iridescenceMap=this.iridescenceMap.toJSON(e).uuid),this.iridescenceThicknessMap&&this.iridescenceThicknessMap.isTexture&&(n.iridescenceThicknessMap=this.iridescenceThicknessMap.toJSON(e).uuid),this.anisotropy!==void 0&&(n.anisotropy=this.anisotropy),this.anisotropyRotation!==void 0&&(n.anisotropyRotation=this.anisotropyRotation),this.anisotropyMap&&this.anisotropyMap.isTexture&&(n.anisotropyMap=this.anisotropyMap.toJSON(e).uuid),this.map&&this.map.isTexture&&(n.map=this.map.toJSON(e).uuid),this.matcap&&this.matcap.isTexture&&(n.matcap=this.matcap.toJSON(e).uuid),this.alphaMap&&this.alphaMap.isTexture&&(n.alphaMap=this.alphaMap.toJSON(e).uuid),this.lightMap&&this.lightMap.isTexture&&(n.lightMap=this.lightMap.toJSON(e).uuid,n.lightMapIntensity=this.lightMapIntensity),this.aoMap&&this.aoMap.isTexture&&(n.aoMap=this.aoMap.toJSON(e).uuid,n.aoMapIntensity=this.aoMapIntensity),this.bumpMap&&this.bumpMap.isTexture&&(n.bumpMap=this.bumpMap.toJSON(e).uuid,n.bumpScale=this.bumpScale),this.normalMap&&this.normalMap.isTexture&&(n.normalMap=this.normalMap.toJSON(e).uuid,n.normalMapType=this.normalMapType,n.normalScale=this.normalScale.toArray()),this.displacementMap&&this.displacementMap.isTexture&&(n.displacementMap=this.displacementMap.toJSON(e).uuid,n.displacementScale=this.displacementScale,n.displacementBias=this.displacementBias),this.roughnessMap&&this.roughnessMap.isTexture&&(n.roughnessMap=this.roughnessMap.toJSON(e).uuid),this.metalnessMap&&this.metalnessMap.isTexture&&(n.metalnessMap=this.metalnessMap.toJSON(e).uuid),this.emissiveMap&&this.emissiveMap.isTexture&&(n.emissiveMap=this.emissiveMap.toJSON(e).uuid),this.specularMap&&this.specularMap.isTexture&&(n.specularMap=this.specularMap.toJSON(e).uuid),this.specularIntensityMap&&this.specularIntensityMap.isTexture&&(n.specularIntensityMap=this.specularIntensityMap.toJSON(e).uuid),this.specularColorMap&&this.specularColorMap.isTexture&&(n.specularColorMap=this.specularColorMap.toJSON(e).uuid),this.envMap&&this.envMap.isTexture&&(n.envMap=this.envMap.toJSON(e).uuid,this.combine!==void 0&&(n.combine=this.combine)),this.envMapRotation!==void 0&&(n.envMapRotation=this.envMapRotation.toArray()),this.envMapIntensity!==void 0&&(n.envMapIntensity=this.envMapIntensity),this.reflectivity!==void 0&&(n.reflectivity=this.reflectivity),this.refractionRatio!==void 0&&(n.refractionRatio=this.refractionRatio),this.gradientMap&&this.gradientMap.isTexture&&(n.gradientMap=this.gradientMap.toJSON(e).uuid),this.transmission!==void 0&&(n.transmission=this.transmission),this.transmissionMap&&this.transmissionMap.isTexture&&(n.transmissionMap=this.transmissionMap.toJSON(e).uuid),this.thickness!==void 0&&(n.thickness=this.thickness),this.thicknessMap&&this.thicknessMap.isTexture&&(n.thicknessMap=this.thicknessMap.toJSON(e).uuid),this.attenuationDistance!==void 0&&(n.attenuationDistance=this.attenuationDistance),this.attenuationColor!==void 0&&(n.attenuationColor=this.attenuationColor.getHex()),this.size!==void 0&&(n.size=this.size),this.sizeAttenuation!==void 0&&(n.sizeAttenuation=this.sizeAttenuation),Array.isArray(this.clippingPlanes)&&this.clippingPlanes.length>0&&(n.clippingPlanes=this.clippingPlanes.map(r=>r.toJSON())),this.rotation!==void 0&&(n.rotation=this.rotation),this.depthPacking!==void 0&&(n.depthPacking=this.depthPacking),this.linewidth!==void 0&&(n.linewidth=this.linewidth),this.linecap!==void 0&&(n.linecap=this.linecap),this.linejoin!==void 0&&(n.linejoin=this.linejoin),this.dashSize!==void 0&&(n.dashSize=this.dashSize),this.gapSize!==void 0&&(n.gapSize=this.gapSize),this.scale!==void 0&&(n.scale=this.scale),this.wireframe!==void 0&&(n.wireframe=this.wireframe),this.wireframeLinewidth!==void 0&&(n.wireframeLinewidth=this.wireframeLinewidth),this.wireframeLinecap!==void 0&&(n.wireframeLinecap=this.wireframeLinecap),this.wireframeLinejoin!==void 0&&(n.wireframeLinejoin=this.wireframeLinejoin),this.flatShading!==void 0&&(n.flatShading=this.flatShading),this.fog!==void 0&&(n.fog=this.fog),Object.keys(this.userData).length>0&&(n.userData=this.userData);function s(r){const a=[];for(const o in r){const d=r[o];delete d.metadata,a.push(d)}return a}if(t){const r=s(e.textures),a=s(e.images);r.length>0&&(n.textures=r),a.length>0&&(n.images=a)}return n}fromJSON(e,t){if(e.uuid!==void 0&&(this.uuid=e.uuid),e.name!==void 0&&(this.name=e.name),e.color!==void 0&&this.color!==void 0&&this.color.setHex(e.color),e.roughness!==void 0&&(this.roughness=e.roughness),e.metalness!==void 0&&(this.metalness=e.metalness),e.sheen!==void 0&&(this.sheen=e.sheen),e.sheenColor!==void 0&&(this.sheenColor=new je().setHex(e.sheenColor)),e.sheenRoughness!==void 0&&(this.sheenRoughness=e.sheenRoughness),e.emissive!==void 0&&this.emissive!==void 0&&this.emissive.setHex(e.emissive),e.specular!==void 0&&this.specular!==void 0&&this.specular.setHex(e.specular),e.specularIntensity!==void 0&&(this.specularIntensity=e.specularIntensity),e.specularColor!==void 0&&this.specularColor!==void 0&&this.specularColor.setHex(e.specularColor),e.shininess!==void 0&&(this.shininess=e.shininess),e.clearcoat!==void 0&&(this.clearcoat=e.clearcoat),e.clearcoatRoughness!==void 0&&(this.clearcoatRoughness=e.clearcoatRoughness),e.diffuseRoughness!==void 0&&(this.diffuseRoughness=e.diffuseRoughness),e.dispersion!==void 0&&(this.dispersion=e.dispersion),e.retroreflectivity!==void 0&&(this.retroreflectivity=e.retroreflectivity),e.iridescence!==void 0&&(this.iridescence=e.iridescence),e.iridescenceIOR!==void 0&&(this.iridescenceIOR=e.iridescenceIOR),e.iridescenceThicknessMinimum!==void 0&&(this.iridescenceThicknessMinimum=e.iridescenceThicknessMinimum),e.iridescenceThicknessMaximum!==void 0&&(this.iridescenceThicknessMaximum=e.iridescenceThicknessMaximum),e.iridescenceThicknessRange!==void 0&&(this.iridescenceThicknessMinimum=e.iridescenceThicknessRange[0],this.iridescenceThicknessMaximum=e.iridescenceThicknessRange[1]),e.transmission!==void 0&&(this.transmission=e.transmission),e.thickness!==void 0&&(this.thickness=e.thickness),e.attenuationDistance!==void 0&&(this.attenuationDistance=e.attenuationDistance),e.attenuationColor!==void 0&&this.attenuationColor!==void 0&&this.attenuationColor.setHex(e.attenuationColor),e.anisotropy!==void 0&&(this.anisotropy=e.anisotropy),e.anisotropyRotation!==void 0&&(this.anisotropyRotation=e.anisotropyRotation),e.fog!==void 0&&(this.fog=e.fog),e.flatShading!==void 0&&(this.flatShading=e.flatShading),e.blending!==void 0&&(this.blending=e.blending),e.combine!==void 0&&(this.combine=e.combine),e.side!==void 0&&(this.side=e.side),e.shadowSide!==void 0&&(this.shadowSide=e.shadowSide),e.opacity!==void 0&&(this.opacity=e.opacity),e.transparent!==void 0&&(this.transparent=e.transparent),e.alphaTest!==void 0&&(this.alphaTest=e.alphaTest),e.alphaHash!==void 0&&(this.alphaHash=e.alphaHash),e.depthFunc!==void 0&&(this.depthFunc=e.depthFunc),e.depthTest!==void 0&&(this.depthTest=e.depthTest),e.depthWrite!==void 0&&(this.depthWrite=e.depthWrite),e.colorWrite!==void 0&&(this.colorWrite=e.colorWrite),e.clippingPlanes!==void 0&&(this.clippingPlanes=e.clippingPlanes.map(n=>new Un().fromJSON(n))),e.clipIntersection!==void 0&&(this.clipIntersection=e.clipIntersection),e.clipShadows!==void 0&&(this.clipShadows=e.clipShadows),e.depthPacking!==void 0&&(this.depthPacking=e.depthPacking),e.blendSrc!==void 0&&(this.blendSrc=e.blendSrc),e.blendDst!==void 0&&(this.blendDst=e.blendDst),e.blendEquation!==void 0&&(this.blendEquation=e.blendEquation),e.blendSrcAlpha!==void 0&&(this.blendSrcAlpha=e.blendSrcAlpha),e.blendDstAlpha!==void 0&&(this.blendDstAlpha=e.blendDstAlpha),e.blendEquationAlpha!==void 0&&(this.blendEquationAlpha=e.blendEquationAlpha),e.blendColor!==void 0&&this.blendColor!==void 0&&this.blendColor.setHex(e.blendColor),e.blendAlpha!==void 0&&(this.blendAlpha=e.blendAlpha),e.stencilWriteMask!==void 0&&(this.stencilWriteMask=e.stencilWriteMask),e.stencilFunc!==void 0&&(this.stencilFunc=e.stencilFunc),e.stencilRef!==void 0&&(this.stencilRef=e.stencilRef),e.stencilFuncMask!==void 0&&(this.stencilFuncMask=e.stencilFuncMask),e.stencilFail!==void 0&&(this.stencilFail=e.stencilFail),e.stencilZFail!==void 0&&(this.stencilZFail=e.stencilZFail),e.stencilZPass!==void 0&&(this.stencilZPass=e.stencilZPass),e.stencilWrite!==void 0&&(this.stencilWrite=e.stencilWrite),e.wireframe!==void 0&&(this.wireframe=e.wireframe),e.wireframeLinewidth!==void 0&&(this.wireframeLinewidth=e.wireframeLinewidth),e.wireframeLinecap!==void 0&&(this.wireframeLinecap=e.wireframeLinecap),e.wireframeLinejoin!==void 0&&(this.wireframeLinejoin=e.wireframeLinejoin),e.rotation!==void 0&&(this.rotation=e.rotation),e.linewidth!==void 0&&(this.linewidth=e.linewidth),e.linecap!==void 0&&(this.linecap=e.linecap),e.linejoin!==void 0&&(this.linejoin=e.linejoin),e.dashSize!==void 0&&(this.dashSize=e.dashSize),e.gapSize!==void 0&&(this.gapSize=e.gapSize),e.scale!==void 0&&(this.scale=e.scale),e.polygonOffset!==void 0&&(this.polygonOffset=e.polygonOffset),e.polygonOffsetFactor!==void 0&&(this.polygonOffsetFactor=e.polygonOffsetFactor),e.polygonOffsetUnits!==void 0&&(this.polygonOffsetUnits=e.polygonOffsetUnits),e.dithering!==void 0&&(this.dithering=e.dithering),e.alphaToCoverage!==void 0&&(this.alphaToCoverage=e.alphaToCoverage),e.premultipliedAlpha!==void 0&&(this.premultipliedAlpha=e.premultipliedAlpha),e.forceSinglePass!==void 0&&(this.forceSinglePass=e.forceSinglePass),e.allowOverride!==void 0&&(this.allowOverride=e.allowOverride),e.visible!==void 0&&(this.visible=e.visible),e.toneMapped!==void 0&&(this.toneMapped=e.toneMapped),e.userData!==void 0&&(this.userData=e.userData),e.vertexColors!==void 0&&(typeof e.vertexColors=="number"?this.vertexColors=e.vertexColors>0:this.vertexColors=e.vertexColors),e.size!==void 0&&(this.size=e.size),e.sizeAttenuation!==void 0&&(this.sizeAttenuation=e.sizeAttenuation),e.map!==void 0&&(this.map=t[e.map]||null),e.matcap!==void 0&&(this.matcap=t[e.matcap]||null),e.alphaMap!==void 0&&(this.alphaMap=t[e.alphaMap]||null),e.bumpMap!==void 0&&(this.bumpMap=t[e.bumpMap]||null),e.bumpScale!==void 0&&(this.bumpScale=e.bumpScale),e.normalMap!==void 0&&(this.normalMap=t[e.normalMap]||null),e.normalMapType!==void 0&&(this.normalMapType=e.normalMapType),e.normalScale!==void 0){let n=e.normalScale;Array.isArray(n)===!1&&(n=[n,n]),this.normalScale=new Xe().fromArray(n)}return e.displacementMap!==void 0&&(this.displacementMap=t[e.displacementMap]||null),e.displacementScale!==void 0&&(this.displacementScale=e.displacementScale),e.displacementBias!==void 0&&(this.displacementBias=e.displacementBias),e.roughnessMap!==void 0&&(this.roughnessMap=t[e.roughnessMap]||null),e.metalnessMap!==void 0&&(this.metalnessMap=t[e.metalnessMap]||null),e.emissiveMap!==void 0&&(this.emissiveMap=t[e.emissiveMap]||null),e.emissiveIntensity!==void 0&&(this.emissiveIntensity=e.emissiveIntensity),e.specularMap!==void 0&&(this.specularMap=t[e.specularMap]||null),e.specularIntensityMap!==void 0&&(this.specularIntensityMap=t[e.specularIntensityMap]||null),e.specularColorMap!==void 0&&(this.specularColorMap=t[e.specularColorMap]||null),e.envMap!==void 0&&(this.envMap=t[e.envMap]||null),e.envMapRotation!==void 0&&this.envMapRotation.fromArray(e.envMapRotation),e.envMapIntensity!==void 0&&(this.envMapIntensity=e.envMapIntensity),e.reflectivity!==void 0&&(this.reflectivity=e.reflectivity),e.refractionRatio!==void 0&&(this.refractionRatio=e.refractionRatio),e.lightMap!==void 0&&(this.lightMap=t[e.lightMap]||null),e.lightMapIntensity!==void 0&&(this.lightMapIntensity=e.lightMapIntensity),e.aoMap!==void 0&&(this.aoMap=t[e.aoMap]||null),e.aoMapIntensity!==void 0&&(this.aoMapIntensity=e.aoMapIntensity),e.gradientMap!==void 0&&(this.gradientMap=t[e.gradientMap]||null),e.clearcoatMap!==void 0&&(this.clearcoatMap=t[e.clearcoatMap]||null),e.clearcoatRoughnessMap!==void 0&&(this.clearcoatRoughnessMap=t[e.clearcoatRoughnessMap]||null),e.clearcoatNormalMap!==void 0&&(this.clearcoatNormalMap=t[e.clearcoatNormalMap]||null),e.clearcoatNormalScale!==void 0&&(this.clearcoatNormalScale=new Xe().fromArray(e.clearcoatNormalScale)),e.diffuseRoughnessMap!==void 0&&(this.diffuseRoughnessMap=t[e.diffuseRoughnessMap]||null),e.iridescenceMap!==void 0&&(this.iridescenceMap=t[e.iridescenceMap]||null),e.iridescenceThicknessMap!==void 0&&(this.iridescenceThicknessMap=t[e.iridescenceThicknessMap]||null),e.transmissionMap!==void 0&&(this.transmissionMap=t[e.transmissionMap]||null),e.thicknessMap!==void 0&&(this.thicknessMap=t[e.thicknessMap]||null),e.anisotropyMap!==void 0&&(this.anisotropyMap=t[e.anisotropyMap]||null),e.sheenColorMap!==void 0&&(this.sheenColorMap=t[e.sheenColorMap]||null),e.sheenRoughnessMap!==void 0&&(this.sheenRoughnessMap=t[e.sheenRoughnessMap]||null),this}clone(){return new this.constructor().copy(this)}copy(e){this.name=e.name,this.blending=e.blending,this.side=e.side,this.vertexColors=e.vertexColors,this.opacity=e.opacity,this.transparent=e.transparent,this.blendSrc=e.blendSrc,this.blendDst=e.blendDst,this.blendEquation=e.blendEquation,this.blendSrcAlpha=e.blendSrcAlpha,this.blendDstAlpha=e.blendDstAlpha,this.blendEquationAlpha=e.blendEquationAlpha,this.blendColor.copy(e.blendColor),this.blendAlpha=e.blendAlpha,this.depthFunc=e.depthFunc,this.depthTest=e.depthTest,this.depthWrite=e.depthWrite,this.stencilWriteMask=e.stencilWriteMask,this.stencilFunc=e.stencilFunc,this.stencilRef=e.stencilRef,this.stencilFuncMask=e.stencilFuncMask,this.stencilFail=e.stencilFail,this.stencilZFail=e.stencilZFail,this.stencilZPass=e.stencilZPass,this.stencilWrite=e.stencilWrite;const t=e.clippingPlanes;let n=null;if(t!==null){const s=t.length;n=new Array(s);for(let r=0;r!==s;++r)n[r]=t[r].clone()}return this.clippingPlanes=n,this.clipIntersection=e.clipIntersection,this.clipShadows=e.clipShadows,this.shadowSide=e.shadowSide,this.colorWrite=e.colorWrite,this.precision=e.precision,this.polygonOffset=e.polygonOffset,this.polygonOffsetFactor=e.polygonOffsetFactor,this.polygonOffsetUnits=e.polygonOffsetUnits,this.dithering=e.dithering,this.alphaTest=e.alphaTest,this.alphaHash=e.alphaHash,this.alphaToCoverage=e.alphaToCoverage,this.premultipliedAlpha=e.premultipliedAlpha,this.forceSinglePass=e.forceSinglePass,this.allowOverride=e.allowOverride,this.visible=e.visible,this.toneMapped=e.toneMapped,this.userData=JSON.parse(JSON.stringify(e.userData)),this}dispose(){this.dispatchEvent({type:"dispose"})}set needsUpdate(e){e===!0&&this.version++}}const yn=new z,sr=new z,Ji=new z,Qi=new z;class Fc{constructor(e=new z,t=new z(0,0,-1)){this.origin=e,this.direction=t}set(e,t){return this.origin.copy(e),this.direction.copy(t),this}copy(e){return this.origin.copy(e.origin),this.direction.copy(e.direction),this}at(e,t){return t.copy(this.origin).addScaledVector(this.direction,e)}lookAt(e){return this.direction.copy(e).sub(this.origin).normalize(),this}recast(e){return this.origin.copy(this.at(e,yn)),this}closestPointToPoint(e,t){t.subVectors(e,this.origin);const n=t.dot(this.direction);return n<0?t.copy(this.origin):t.copy(this.origin).addScaledVector(this.direction,n)}distanceToPoint(e){return Math.sqrt(this.distanceSqToPoint(e))}distanceSqToPoint(e){const t=yn.subVectors(e,this.origin).dot(this.direction);return t<0?this.origin.distanceToSquared(e):(yn.copy(this.origin).addScaledVector(this.direction,t),yn.distanceToSquared(e))}distanceSqToSegment(e,t,n,s){sr.copy(e).add(t).multiplyScalar(.5),Ji.copy(t).sub(e).normalize(),Qi.copy(this.origin).sub(sr);const r=e.distanceTo(t)*.5,a=-this.direction.dot(Ji),o=Qi.dot(this.direction),d=-Qi.dot(Ji),c=Qi.lengthSq(),m=Math.abs(1-a*a);let x,f,g,l;if(m>0)if(x=a*d-o,f=a*o-d,l=r*m,x>=0)if(f>=-l)if(f<=l){const T=1/m;x*=T,f*=T,g=x*(x+a*f+2*o)+f*(a*x+f+2*d)+c}else f=r,x=Math.max(0,-(a*f+o)),g=-x*x+f*(f+2*d)+c;else f=-r,x=Math.max(0,-(a*f+o)),g=-x*x+f*(f+2*d)+c;else f<=-l?(x=Math.max(0,-(-a*r+o)),f=x>0?-r:Math.min(Math.max(-r,-d),r),g=-x*x+f*(f+2*d)+c):f<=l?(x=0,f=Math.min(Math.max(-r,-d),r),g=f*(f+2*d)+c):(x=Math.max(0,-(a*r+o)),f=x>0?r:Math.min(Math.max(-r,-d),r),g=-x*x+f*(f+2*d)+c);else f=a>0?-r:r,x=Math.max(0,-(a*f+o)),g=-x*x+f*(f+2*d)+c;return n&&n.copy(this.origin).addScaledVector(this.direction,x),s&&s.copy(sr).addScaledVector(Ji,f),g}intersectSphere(e,t){if(e.radius<0)return null;yn.subVectors(e.center,this.origin);const n=yn.dot(this.direction),s=yn.dot(yn)-n*n,r=e.radius*e.radius;if(s>r)return null;const a=Math.sqrt(r-s),o=n-a,d=n+a;return d<0?null:o<0?this.at(d,t):this.at(o,t)}intersectsSphere(e){return e.radius<0?!1:this.distanceSqToPoint(e.center)<=e.radius*e.radius}distanceToPlane(e){const t=e.normal.dot(this.direction);if(t===0)return e.distanceToPoint(this.origin)===0?0:null;const n=-(this.origin.dot(e.normal)+e.constant)/t;return n>=0?n:null}intersectPlane(e,t){const n=this.distanceToPlane(e);return n===null?null:this.at(n,t)}intersectsPlane(e){const t=e.distanceToPoint(this.origin);return t===0||e.normal.dot(this.direction)*t<0}intersectBox(e,t){let n,s,r,a,o,d;const c=1/this.direction.x,m=1/this.direction.y,x=1/this.direction.z,f=this.origin;return c>=0?(n=(e.min.x-f.x)*c,s=(e.max.x-f.x)*c):(n=(e.max.x-f.x)*c,s=(e.min.x-f.x)*c),m>=0?(r=(e.min.y-f.y)*m,a=(e.max.y-f.y)*m):(r=(e.max.y-f.y)*m,a=(e.min.y-f.y)*m),n>a||r>s||((r>n||isNaN(n))&&(n=r),(a<s||isNaN(s))&&(s=a),x>=0?(o=(e.min.z-f.z)*x,d=(e.max.z-f.z)*x):(o=(e.max.z-f.z)*x,d=(e.min.z-f.z)*x),n>d||o>s)||((o>n||n!==n)&&(n=o),(d<s||s!==s)&&(s=d),s<0)?null:this.at(n>=0?n:s,t)}intersectsBox(e){return this.intersectBox(e,yn)!==null}intersectTriangle(e,t,n,s,r){const a=this.origin,o=this.direction,d=o.x,c=o.y,m=o.z,x=e.x-a.x,f=e.y-a.y,g=e.z-a.z,l=t.x-a.x,T=t.y-a.y,p=t.z-a.z,u=n.x-a.x,b=n.y-a.y,y=n.z-a.z,E=Math.abs(d),R=Math.abs(c),A=Math.abs(m);let h,M,I,C,U,F,X,L,W,K,H,ne;if(E>=R&&E>=A?(I=d,F=x,W=l,ne=u,d>=0?(h=c,M=m,C=f,U=g,X=T,L=p,K=b,H=y):(h=m,M=c,C=g,U=f,X=p,L=T,K=y,H=b)):R>=A?(I=c,F=f,W=T,ne=b,c>=0?(h=m,M=d,C=g,U=x,X=p,L=l,K=y,H=u):(h=d,M=m,C=x,U=g,X=l,L=p,K=u,H=y)):(I=m,F=g,W=p,ne=y,m>=0?(h=d,M=c,C=x,U=f,X=l,L=T,K=u,H=b):(h=c,M=d,C=f,U=x,X=T,L=l,K=b,H=u)),I===0)return null;const q=h/I,ee=M/I,te=1/I,Me=C-q*F,Le=U-ee*F,$=X-q*W,pe=L-ee*W,ve=K-q*ne,Ae=H-ee*ne,me=ve*pe-Ae*$,Ie=Me*Ae-Le*ve,gt=$*Le-pe*Me;if(s){if(me<0||Ie<0||gt<0)return null}else if((me<0||Ie<0||gt<0)&&(me>0||Ie>0||gt>0))return null;const Re=me+Ie+gt;if(Re===0)return null;const et=te*(me*F+Ie*W+gt*ne);return(Re>0?et<0:et>0)?null:this.at(et/Re,r)}applyMatrix4(e){return this.origin.applyMatrix4(e),this.direction.transformDirection(e),this}equals(e){return e.origin.equals(this.origin)&&e.direction.equals(this.direction)}clone(){return new this.constructor().copy(this)}}class Oc extends ki{constructor(e){super(),this.isMeshBasicMaterial=!0,this.type="MeshBasicMaterial",this.color=new je(16777215),this.map=null,this.lightMap=null,this.lightMapIntensity=1,this.aoMap=null,this.aoMapIntensity=1,this.specularMap=null,this.alphaMap=null,this.envMap=null,this.envMapRotation=new Gn,this.combine=Lo,this.reflectivity=1,this.refractionRatio=.98,this.wireframe=!1,this.wireframeLinewidth=1,this.wireframeLinecap="round",this.wireframeLinejoin="round",this.fog=!0,this.setValues(e)}copy(e){return super.copy(e),this.color.copy(e.color),this.map=e.map,this.lightMap=e.lightMap,this.lightMapIntensity=e.lightMapIntensity,this.aoMap=e.aoMap,this.aoMapIntensity=e.aoMapIntensity,this.specularMap=e.specularMap,this.alphaMap=e.alphaMap,this.envMap=e.envMap,this.envMapRotation.copy(e.envMapRotation),this.combine=e.combine,this.reflectivity=e.reflectivity,this.refractionRatio=e.refractionRatio,this.wireframe=e.wireframe,this.wireframeLinewidth=e.wireframeLinewidth,this.wireframeLinecap=e.wireframeLinecap,this.wireframeLinejoin=e.wireframeLinejoin,this.fog=e.fog,this}}const Ka=new mt,Kn=new Fc,ji=new la,Za=new z,es=new z,ts=new z,ns=new z,rr=new z,is=new z,$a=new z,ss=new z;class ht extends Ut{constructor(e=new cn,t=new Oc){super(),this.isMesh=!0,this.type="Mesh",this.geometry=e,this.material=t,this.morphTargetDictionary=void 0,this.morphTargetInfluences=void 0,this.count=1,this.updateMorphTargets()}copy(e,t){return super.copy(e,t),e.morphTargetInfluences!==void 0?this.morphTargetInfluences=e.morphTargetInfluences.slice():this.morphTargetInfluences=void 0,e.morphTargetDictionary!==void 0?this.morphTargetDictionary=Object.assign({},e.morphTargetDictionary):this.morphTargetDictionary=void 0,this.material=Array.isArray(e.material)?e.material.slice():e.material,this.geometry=e.geometry,this}updateMorphTargets(){const t=this.geometry.morphAttributes,n=Object.keys(t);if(n.length>0){const s=t[n[0]];if(s!==void 0){this.morphTargetInfluences=[],this.morphTargetDictionary={};for(let r=0,a=s.length;r<a;r++){const o=s[r].name||String(r);this.morphTargetInfluences.push(0),this.morphTargetDictionary[o]=r}}}}getVertexPosition(e,t){const n=this.geometry,s=n.attributes.position,r=n.morphAttributes.position,a=n.morphTargetsRelative;t.fromBufferAttribute(s,e);const o=this.morphTargetInfluences;if(r&&o){is.set(0,0,0);for(let d=0,c=r.length;d<c;d++){const m=o[d],x=r[d];m!==0&&(rr.fromBufferAttribute(x,e),a?is.addScaledVector(rr,m):is.addScaledVector(rr.sub(t),m))}t.add(is)}return t}intersectsFrustum(e){return e.intersectsObject(this)}raycast(e,t){const n=this.geometry,s=this.material,r=this.matrixWorld;s!==void 0&&(n.boundingSphere===null&&n.computeBoundingSphere(),ji.copy(n.boundingSphere),ji.applyMatrix4(r),Kn.copy(e.ray).recast(e.near),!(ji.containsPoint(Kn.origin)===!1&&(Kn.intersectSphere(ji,Za)===null||Kn.origin.distanceToSquared(Za)>(e.far-e.near)**2))&&(Ka.copy(r).invert(),Kn.copy(e.ray).applyMatrix4(Ka),!(n.boundingBox!==null&&Kn.intersectsBox(n.boundingBox)===!1)&&this._computeIntersections(e,t,Kn)))}_computeIntersections(e,t,n){let s;const r=this.geometry,a=this.material,o=r.index,d=r.attributes.position,c=r.attributes.uv,m=r.attributes.uv1,x=r.attributes.normal,f=r.groups,g=r.drawRange;if(o!==null)if(Array.isArray(a))for(let l=0,T=f.length;l<T;l++){const p=f[l],u=a[p.materialIndex],b=Math.max(p.start,g.start),y=Math.min(o.count,Math.min(p.start+p.count,g.start+g.count));for(let E=b,R=y;E<R;E+=3){const A=o.getX(E),h=o.getX(E+1),M=o.getX(E+2);s=rs(this,u,e,n,c,m,x,A,h,M),s&&(s.faceIndex=Math.floor(E/3),s.face.materialIndex=p.materialIndex,t.push(s))}}else{const l=Math.max(0,g.start),T=Math.min(o.count,g.start+g.count);for(let p=l,u=T;p<u;p+=3){const b=o.getX(p),y=o.getX(p+1),E=o.getX(p+2);s=rs(this,a,e,n,c,m,x,b,y,E),s&&(s.faceIndex=Math.floor(p/3),t.push(s))}}else if(d!==void 0)if(Array.isArray(a))for(let l=0,T=f.length;l<T;l++){const p=f[l],u=a[p.materialIndex],b=Math.max(p.start,g.start),y=Math.min(d.count,Math.min(p.start+p.count,g.start+g.count));for(let E=b,R=y;E<R;E+=3){const A=E,h=E+1,M=E+2;s=rs(this,u,e,n,c,m,x,A,h,M),s&&(s.faceIndex=Math.floor(E/3),s.face.materialIndex=p.materialIndex,t.push(s))}}else{const l=Math.max(0,g.start),T=Math.min(d.count,g.start+g.count);for(let p=l,u=T;p<u;p+=3){const b=p,y=p+1,E=p+2;s=rs(this,a,e,n,c,m,x,b,y,E),s&&(s.faceIndex=Math.floor(p/3),t.push(s))}}}}function Bc(i,e,t,n,s,r,a,o){let d;if(e.side===Xt?d=n.intersectTriangle(a,r,s,!0,o):d=n.intersectTriangle(s,r,a,e.side===Jn,o),d===null)return null;ss.copy(o),ss.applyMatrix4(i.matrixWorld);const c=t.ray.origin.distanceTo(ss);return c<t.near||c>t.far?null:{distance:c,point:ss.clone(),object:i}}function rs(i,e,t,n,s,r,a,o,d,c){i.getVertexPosition(o,es),i.getVertexPosition(d,ts),i.getVertexPosition(c,ns);const m=Bc(i,e,t,n,es,ts,ns,$a);if(m){const x=new z;on.getBarycoord($a,es,ts,ns,x),s&&(m.uv=on.getInterpolatedAttribute(s,o,d,c,x,new Xe)),r&&(m.uv1=on.getInterpolatedAttribute(r,o,d,c,x,new Xe)),a&&(m.normal=on.getInterpolatedAttribute(a,o,d,c,x,new z),m.normal.dot(n.direction)>0&&m.normal.multiplyScalar(-1));const f={a:o,b:d,c,normal:new z,materialIndex:0};on.getNormal(es,ts,ns,f.normal),m.face=f,m.barycoord=x}return m}class kc extends zt{constructor(e=null,t=1,n=1,s,r,a,o,d,c=Ct,m=Ct,x,f){super(null,a,o,d,c,m,s,r,x,f),this.isDataTexture=!0,this.image={data:e,width:t,height:n},this.generateMipmaps=!1,this.flipY=!1,this.unpackAlignment=1}}const Zn=new la,zc=new Xe(.5,.5),as=new z;class Fi{constructor(e=new Un,t=new Un,n=new Un,s=new Un,r=new Un,a=new Un){this.planes=[e,t,n,s,r,a]}set(e,t,n,s,r,a){const o=this.planes;return o[0].copy(e),o[1].copy(t),o[2].copy(n),o[3].copy(s),o[4].copy(r),o[5].copy(a),this}copy(e){const t=this.planes;for(let n=0;n<6;n++)t[n].copy(e.planes[n]);return this}setFromProjectionMatrix(e,t=ln,n=!1){const s=this.planes,r=e.elements,a=r[0],o=r[1],d=r[2],c=r[3],m=r[4],x=r[5],f=r[6],g=r[7],l=r[8],T=r[9],p=r[10],u=r[11],b=r[12],y=r[13],E=r[14],R=r[15];if(s[0].setComponents(c-a,g-m,u-l,R-b).normalize(),s[1].setComponents(c+a,g+m,u+l,R+b).normalize(),s[2].setComponents(c+o,g+x,u+T,R+y).normalize(),s[3].setComponents(c-o,g-x,u-T,R-y).normalize(),n)s[4].setComponents(d,f,p,E).normalize(),s[5].setComponents(c-d,g-f,u-p,R-E).normalize();else if(s[4].setComponents(c-d,g-f,u-p,R-E).normalize(),t===ln)s[5].setComponents(c+d,g+f,u+p,R+E).normalize();else if(t===Ui)s[5].setComponents(d,f,p,E).normalize();else throw new Error("THREE.Frustum.setFromProjectionMatrix(): Invalid coordinate system: "+t);return this}intersectsObject(e){if(e.boundingSphere!==void 0)e.boundingSphere===null&&e.computeBoundingSphere(),Zn.copy(e.boundingSphere).applyMatrix4(e.matrixWorld);else{const t=e.geometry;t.boundingSphere===null&&t.computeBoundingSphere(),Zn.copy(t.boundingSphere).applyMatrix4(e.matrixWorld)}return this.intersectsSphere(Zn)}intersectsSprite(e){Zn.center.set(0,0,0);const t=zc.distanceTo(e.center);return Zn.radius=.7071067811865476+t,Zn.applyMatrix4(e.matrixWorld),this.intersectsSphere(Zn)}intersectsSphere(e){const t=this.planes,n=e.center,s=-e.radius;for(let r=0;r<6;r++)if(t[r].distanceToPoint(n)<s)return!1;return!0}intersectsBox(e){const t=this.planes;for(let n=0;n<6;n++){const s=t[n];if(as.x=s.normal.x>0?e.max.x:e.min.x,as.y=s.normal.y>0?e.max.y:e.min.y,as.z=s.normal.z>0?e.max.z:e.min.z,s.distanceToPoint(as)<0)return!1}return!0}containsPoint(e){const t=this.planes;for(let n=0;n<6;n++)if(t[n].distanceToPoint(e)<0)return!1;return!0}clone(){return new this.constructor().copy(this)}}const Ja=new mt;class ca{constructor(){this.coordinateSystem=ln,this._frustums=[],this._count=0}setFromArrayCamera(e){const t=e.cameras,n=this._frustums;for(let s=0;s<t.length;s++){const r=t[s];Ja.multiplyMatrices(r.projectionMatrix,r.matrixWorldInverse),n[s]===void 0&&(n[s]=new Fi),n[s].setFromProjectionMatrix(Ja,r.coordinateSystem,r.reversedDepth)}return this._count=t.length,this}getFrustum(e){return this._frustums[e]}intersectsObject(e){const t=this._frustums;for(let n=0;n<this._count;n++)if(t[n].intersectsObject(e))return!0;return!1}intersectsSprite(e){const t=this._frustums;for(let n=0;n<this._count;n++)if(t[n].intersectsSprite(e))return!0;return!1}intersectsSphere(e){const t=this._frustums;for(let n=0;n<this._count;n++)if(t[n].intersectsSphere(e))return!0;return!1}intersectsBox(e){const t=this._frustums;for(let n=0;n<this._count;n++)if(t[n].intersectsBox(e))return!0;return!1}containsPoint(e){const t=this._frustums;for(let n=0;n<this._count;n++)if(t[n].containsPoint(e))return!0;return!1}copy(e){this.coordinateSystem=e.coordinateSystem;const t=this._frustums,n=e._frustums;for(let s=0;s<e._count;s++)t[s]===void 0&&(t[s]=new Fi),t[s].copy(n[s]);return this._count=e._count,this}clone(){return new ca().copy(this)}}class tl extends zt{constructor(e=[],t=xi,n,s,r,a,o,d,c,m){super(e,t,n,s,r,a,o,d,c,m),this.isCubeTexture=!0,this.flipY=!1}get images(){return this.image}set images(e){this.image=e}}class vi extends zt{constructor(e,t,n=gn,s,r,a,o=Ct,d=Ct,c,m=zn,x=1){if(m!==zn&&m!==Bn)throw new Error("THREE.DepthTexture: format must be either THREE.DepthFormat or THREE.DepthStencilFormat");const f={width:e,height:t,depth:x};super(f,s,r,a,o,d,m,n,c),this.isDepthTexture=!0,this.flipY=!1,this.generateMipmaps=!1,this.compareFunction=null}copy(e){return super.copy(e),this.source=new oa(Object.assign({},e.image)),this.compareFunction=e.compareFunction,this}toJSON(e){const t=super.toJSON(e);return t.compareFunction=this.compareFunction,t}}class nl extends vi{constructor(e,t=gn,n=xi,s,r,a=Ct,o=Ct,d,c=zn){const m={width:e,height:e,depth:1},x=[m,m,m,m,m,m];super(e,e,t,n,s,r,a,o,d,c),this.image=x,this.isCubeDepthTexture=!0,this.isCubeTexture=!0}get images(){return this.image}set images(e){this.image=e}}class il extends zt{constructor(e=null){super(),this.sourceTexture=e,this.isExternalTexture=!0}copy(e){return super.copy(e),this.sourceTexture=e.sourceTexture,this}}class an extends cn{constructor(e=1,t=1,n=1,s=1,r=1,a=1){super(),this.type="BoxGeometry",this.parameters={width:e,height:t,depth:n,widthSegments:s,heightSegments:r,depthSegments:a};const o=this;s=Math.floor(s),r=Math.floor(r),a=Math.floor(a);const d=[],c=[],m=[],x=[];let f=0,g=0;l("z","y","x",-1,-1,n,t,e,a,r,0),l("z","y","x",1,-1,n,t,-e,a,r,1),l("x","z","y",1,1,e,n,t,s,a,2),l("x","z","y",1,-1,e,n,-t,s,a,3),l("x","y","z",1,-1,e,t,n,s,r,4),l("x","y","z",-1,-1,e,t,-n,s,r,5),this.setIndex(d),this.setAttribute("position",new _t(c,3)),this.setAttribute("normal",new _t(m,3)),this.setAttribute("uv",new _t(x,2));function l(T,p,u,b,y,E,R,A,h,M,I){const C=E/h,U=R/M,F=E/2,X=R/2,L=A/2,W=h+1,K=M+1;let H=0,ne=0;const q=new z;for(let ee=0;ee<K;ee++){const te=ee*U-X;for(let Me=0;Me<W;Me++){const Le=Me*C-F;q[T]=Le*b,q[p]=te*y,q[u]=L,c.push(q.x,q.y,q.z),q[T]=0,q[p]=0,q[u]=A>0?1:-1,m.push(q.x,q.y,q.z),x.push(Me/h),x.push(1-ee/M),H+=1}}for(let ee=0;ee<M;ee++)for(let te=0;te<h;te++){const Me=f+te+W*ee,Le=f+te+W*(ee+1),$=f+(te+1)+W*(ee+1),pe=f+(te+1)+W*ee;d.push(Me,Le,pe),d.push(Le,$,pe),ne+=6}o.addGroup(g,ne,I),g+=ne,f+=H}}copy(e){return super.copy(e),this.parameters=Object.assign({},e.parameters),this}static fromJSON(e){return new an(e.width,e.height,e.depth,e.widthSegments,e.heightSegments,e.depthSegments)}}class Es extends cn{constructor(e=1,t=1,n=1,s=32,r=1,a=!1,o=0,d=Math.PI*2){super(),this.type="CylinderGeometry",this.parameters={radiusTop:e,radiusBottom:t,height:n,radialSegments:s,heightSegments:r,openEnded:a,thetaStart:o,thetaLength:d};const c=this;s=Math.floor(s),r=Math.floor(r);const m=[],x=[],f=[],g=[];let l=0;const T=[],p=n/2;let u=0;b(),a===!1&&(e>0&&y(!0),t>0&&y(!1)),this.setIndex(m),this.setAttribute("position",new _t(x,3)),this.setAttribute("normal",new _t(f,3)),this.setAttribute("uv",new _t(g,2));function b(){const E=new z,R=new z;let A=0;const h=(t-e)/n;for(let M=0;M<=r;M++){const I=[],C=M/r,U=C*(t-e)+e;for(let F=0;F<=s;F++){const X=F/s,L=X*d+o,W=Math.sin(L),K=Math.cos(L);R.x=U*W,R.y=-C*n+p,R.z=U*K,x.push(R.x,R.y,R.z),E.set(W,h,K).normalize(),f.push(E.x,E.y,E.z),g.push(X,1-C),I.push(l++)}T.push(I)}for(let M=0;M<s;M++)for(let I=0;I<r;I++){const C=T[I][M],U=T[I+1][M],F=T[I+1][M+1],X=T[I][M+1];(e>0||I!==0)&&(m.push(C,U,X),A+=3),(t>0||I!==r-1)&&(m.push(U,F,X),A+=3)}c.addGroup(u,A,0),u+=A}function y(E){const R=l,A=new Xe,h=new z;let M=0;const I=E===!0?e:t,C=E===!0?1:-1;for(let F=1;F<=s;F++)x.push(0,p*C,0),f.push(0,C,0),g.push(.5,.5),l++;const U=l;for(let F=0;F<=s;F++){const L=F/s*d+o,W=Math.cos(L),K=Math.sin(L);h.x=I*K,h.y=p*C,h.z=I*W,x.push(h.x,h.y,h.z),f.push(0,C,0),A.x=W*.5+.5,A.y=K*.5*C+.5,g.push(A.x,A.y),l++}for(let F=0;F<s;F++){const X=R+F,L=U+F;E===!0?m.push(L,L+1,X):m.push(L+1,L,X),M+=3}c.addGroup(u,M,E===!0?1:2),u+=M}}copy(e){return super.copy(e),this.parameters=Object.assign({},e.parameters),this}static fromJSON(e){return new Es(e.radiusTop,e.radiusBottom,e.height,e.radialSegments,e.heightSegments,e.openEnded,e.thetaStart,e.thetaLength)}}class da extends cn{constructor(e=[],t=[],n=1,s=0){super(),this.type="PolyhedronGeometry",this.parameters={vertices:e,indices:t,radius:n,detail:s};const r=[],a=[];o(s),c(n),m(),this.setAttribute("position",new _t(r,3)),this.setAttribute("normal",new _t(r.slice(),3)),this.setAttribute("uv",new _t(a,2)),s===0?this.computeVertexNormals():this.normalizeNormals();function o(b){const y=new z,E=new z,R=new z;for(let A=0;A<t.length;A+=3)g(t[A+0],y),g(t[A+1],E),g(t[A+2],R),d(y,E,R,b)}function d(b,y,E,R){const A=R+1,h=[];for(let M=0;M<=A;M++){h[M]=[];const I=b.clone().lerp(E,M/A),C=y.clone().lerp(E,M/A),U=A-M;for(let F=0;F<=U;F++)F===0&&M===A?h[M][F]=I:h[M][F]=I.clone().lerp(C,F/U)}for(let M=0;M<A;M++)for(let I=0;I<2*(A-M)-1;I++){const C=Math.floor(I/2);I%2===0?(f(h[M][C+1]),f(h[M+1][C]),f(h[M][C])):(f(h[M][C+1]),f(h[M+1][C+1]),f(h[M+1][C]))}}function c(b){const y=new z;for(let E=0;E<r.length;E+=3)y.x=r[E+0],y.y=r[E+1],y.z=r[E+2],y.normalize().multiplyScalar(b),r[E+0]=y.x,r[E+1]=y.y,r[E+2]=y.z}function m(){const b=new z;for(let y=0;y<r.length;y+=3){b.x=r[y+0],b.y=r[y+1],b.z=r[y+2];const E=p(b)/2/Math.PI+.5,R=u(b)/Math.PI+.5;a.push(E,1-R)}l(),x()}function x(){for(let b=0;b<a.length;b+=6){const y=a[b+0],E=a[b+2],R=a[b+4],A=Math.max(y,E,R),h=Math.min(y,E,R);A>.9&&h<.1&&(y<.2&&(a[b+0]+=1),E<.2&&(a[b+2]+=1),R<.2&&(a[b+4]+=1))}}function f(b){r.push(b.x,b.y,b.z)}function g(b,y){const E=b*3;y.x=e[E+0],y.y=e[E+1],y.z=e[E+2]}function l(){const b=new z,y=new z,E=new z,R=new z,A=new Xe,h=new Xe,M=new Xe;for(let I=0,C=0;I<r.length;I+=9,C+=6){b.set(r[I+0],r[I+1],r[I+2]),y.set(r[I+3],r[I+4],r[I+5]),E.set(r[I+6],r[I+7],r[I+8]),A.set(a[C+0],a[C+1]),h.set(a[C+2],a[C+3]),M.set(a[C+4],a[C+5]),R.copy(b).add(y).add(E).divideScalar(3);const U=p(R);T(A,C+0,b,U),T(h,C+2,y,U),T(M,C+4,E,U)}}function T(b,y,E,R){R<0&&b.x===1&&(a[y]=b.x-1),E.x===0&&E.z===0&&(a[y]=R/2/Math.PI+.5)}function p(b){return Math.atan2(b.z,-b.x)}function u(b){return Math.atan2(-b.y,Math.sqrt(b.x*b.x+b.z*b.z))}}copy(e){return super.copy(e),this.parameters=Object.assign({},e.parameters),this}static fromJSON(e){return new da(e.vertices,e.indices,e.radius,e.detail)}}class ua extends da{constructor(e=1,t=0){const n=[1,0,0,-1,0,0,0,1,0,0,-1,0,0,0,1,0,0,-1],s=[0,2,4,0,4,3,0,3,5,0,5,2,1,2,5,1,5,3,1,3,4,1,4,2];super(n,s,e,t),this.type="OctahedronGeometry",this.parameters={radius:e,detail:t}}static fromJSON(e){return new ua(e.radius,e.detail)}}class Cs extends cn{constructor(e=1,t=1,n=1,s=1){super(),this.type="PlaneGeometry",this.parameters={width:e,height:t,widthSegments:n,heightSegments:s};const r=e/2,a=t/2,o=Math.floor(n),d=Math.floor(s),c=o+1,m=d+1,x=e/o,f=t/d,g=[],l=[],T=[],p=[];for(let u=0;u<m;u++){const b=u*f-a;for(let y=0;y<c;y++){const E=y*x-r;l.push(E,-b,0),T.push(0,0,1),p.push(y/o),p.push(1-u/d)}}for(let u=0;u<d;u++)for(let b=0;b<o;b++){const y=b+c*u,E=b+c*(u+1),R=b+1+c*(u+1),A=b+1+c*u;g.push(y,E,A),g.push(E,R,A)}this.setIndex(g),this.setAttribute("position",new _t(l,3)),this.setAttribute("normal",new _t(T,3)),this.setAttribute("uv",new _t(p,2))}copy(e){return super.copy(e),this.parameters=Object.assign({},e.parameters),this}static fromJSON(e){return new Cs(e.width,e.height,e.widthSegments,e.heightSegments)}}class Ts extends cn{constructor(e=1,t=32,n=16,s=0,r=Math.PI*2,a=0,o=Math.PI){super(),this.type="SphereGeometry",this.parameters={radius:e,widthSegments:t,heightSegments:n,phiStart:s,phiLength:r,thetaStart:a,thetaLength:o},t=Math.max(3,Math.floor(t)),n=Math.max(2,Math.floor(n));const d=Math.min(a+o,Math.PI);let c=0;const m=[],x=new z,f=new z,g=[],l=[],T=[],p=[];for(let u=0;u<=n;u++){const b=[],y=u/n,E=a+y*o,R=e*Math.cos(E),A=Math.sqrt(e*e-R*R);let h=0;u===0&&a===0?h=.5/t:u===n&&d===Math.PI&&(h=-.5/t);for(let M=0;M<=t;M++){const I=M/t,C=s+I*r;x.x=-A*Math.cos(C),x.y=R,x.z=A*Math.sin(C),l.push(x.x,x.y,x.z),f.copy(x).normalize(),T.push(f.x,f.y,f.z),p.push(I+h,1-y),b.push(c++)}m.push(b)}for(let u=0;u<n;u++)for(let b=0;b<t;b++){const y=m[u][b+1],E=m[u][b],R=m[u+1][b],A=m[u+1][b+1];(u!==0||a>0)&&g.push(y,E,A),(u!==n-1||d<Math.PI)&&g.push(E,R,A)}this.setIndex(g),this.setAttribute("position",new _t(l,3)),this.setAttribute("normal",new _t(T,3)),this.setAttribute("uv",new _t(p,2))}copy(e){return super.copy(e),this.parameters=Object.assign({},e.parameters),this}static fromJSON(e){return new Ts(e.radius,e.widthSegments,e.heightSegments,e.phiStart,e.phiLength,e.thetaStart,e.thetaLength)}}class ws extends cn{constructor(e=1,t=.4,n=12,s=48,r=Math.PI*2,a=0,o=Math.PI*2){super(),this.type="TorusGeometry",this.parameters={radius:e,tube:t,radialSegments:n,tubularSegments:s,arc:r,thetaStart:a,thetaLength:o},n=Math.floor(n),s=Math.floor(s);const d=[],c=[],m=[],x=[],f=new z,g=new z,l=new z;for(let T=0;T<=n;T++){const p=a+T/n*o;for(let u=0;u<=s;u++){const b=u/s*r;g.x=(e+t*Math.cos(p))*Math.cos(b),g.y=(e+t*Math.cos(p))*Math.sin(b),g.z=t*Math.sin(p),c.push(g.x,g.y,g.z),f.x=e*Math.cos(b),f.y=e*Math.sin(b),l.subVectors(g,f).normalize(),m.push(l.x,l.y,l.z),x.push(u/s),x.push(T/n)}}for(let T=1;T<=n;T++)for(let p=1;p<=s;p++){const u=(s+1)*T+p-1,b=(s+1)*(T-1)+p-1,y=(s+1)*(T-1)+p,E=(s+1)*T+p;d.push(u,b,E),d.push(b,y,E)}this.setIndex(d),this.setAttribute("position",new _t(c,3)),this.setAttribute("normal",new _t(m,3)),this.setAttribute("uv",new _t(x,2))}copy(e){return super.copy(e),this.parameters=Object.assign({},e.parameters),this}static fromJSON(e){return new ws(e.radius,e.tube,e.radialSegments,e.tubularSegments,e.arc,e.thetaStart,e.thetaLength)}}function Si(i){const e={};for(const t in i){e[t]={};for(const n in i[t]){const s=i[t][n];if(Qa(s))s.isRenderTargetTexture?(De("UniformsUtils: Textures of render targets cannot be cloned via cloneUniforms() or mergeUniforms()."),e[t][n]=null):e[t][n]=s.clone();else if(Array.isArray(s))if(Qa(s[0])){const r=[];for(let a=0,o=s.length;a<o;a++)r[a]=s[a].clone();e[t][n]=r}else e[t][n]=s.slice();else e[t][n]=s}}return e}function kt(i){const e={};for(let t=0;t<i.length;t++){const n=Si(i[t]);for(const s in n)e[s]=n[s]}return e}function Qa(i){return i&&(i.isColor||i.isMatrix3||i.isMatrix4||i.isVector2||i.isVector3||i.isVector4||i.isTexture||i.isQuaternion)}function Gc(i){const e=[];for(let t=0;t<i.length;t++)e.push(i[t].clone());return e}function sl(i){const e=i.getRenderTarget();return e===null?i.outputColorSpace:e.isXRRenderTarget===!0?e.texture.colorSpace:Ze.workingColorSpace}const Hc={clone:Si,merge:kt};var Vc=`void main() {
	gl_Position = projectionMatrix * modelViewMatrix * vec4( position, 1.0 );
}`,Wc=`void main() {
	gl_FragColor = vec4( 1.0, 0.0, 0.0, 1.0 );
}`;class jn extends ki{constructor(e){super(),this.isShaderMaterial=!0,this.type="ShaderMaterial",this.defines={},this.uniforms={},this.uniformsGroups=[],this.vertexShader=Vc,this.fragmentShader=Wc,this.linewidth=1,this.wireframe=!1,this.wireframeLinewidth=1,this.fog=!1,this.lights=!1,this.clipping=!1,this.forceSinglePass=!0,this.extensions={clipCullDistance:!1,multiDraw:!1},this.defaultAttributeValues={color:[1,1,1],uv:[0,0],uv1:[0,0]},this.index0AttributeName=void 0,this.uniformsNeedUpdate=!1,this.glslVersion=null,e!==void 0&&this.setValues(e)}copy(e){return super.copy(e),this.fragmentShader=e.fragmentShader,this.vertexShader=e.vertexShader,this.uniforms=Si(e.uniforms),this.uniformsGroups=Gc(e.uniformsGroups),this.defines=Object.assign({},e.defines),this.wireframe=e.wireframe,this.wireframeLinewidth=e.wireframeLinewidth,this.fog=e.fog,this.lights=e.lights,this.clipping=e.clipping,this.extensions=Object.assign({},e.extensions),this.glslVersion=e.glslVersion,this.defaultAttributeValues=Object.assign({},e.defaultAttributeValues),this.index0AttributeName=e.index0AttributeName,this.uniformsNeedUpdate=e.uniformsNeedUpdate,this}toJSON(e){const t=super.toJSON(e);t.glslVersion=this.glslVersion,t.uniforms={};for(const s in this.uniforms){const a=this.uniforms[s].value;a&&a.isTexture?t.uniforms[s]={type:"t",value:a.toJSON(e).uuid}:a&&a.isColor?t.uniforms[s]={type:"c",value:a.getHex()}:a&&a.isVector2?t.uniforms[s]={type:"v2",value:a.toArray()}:a&&a.isVector3?t.uniforms[s]={type:"v3",value:a.toArray()}:a&&a.isVector4?t.uniforms[s]={type:"v4",value:a.toArray()}:a&&a.isMatrix3?t.uniforms[s]={type:"m3",value:a.toArray()}:a&&a.isMatrix4?t.uniforms[s]={type:"m4",value:a.toArray()}:t.uniforms[s]={value:a}}Object.keys(this.defines).length>0&&(t.defines=this.defines),t.vertexShader=this.vertexShader,t.fragmentShader=this.fragmentShader,t.lights=this.lights,t.clipping=this.clipping;const n={};for(const s in this.extensions)this.extensions[s]===!0&&(n[s]=!0);return Object.keys(n).length>0&&(t.extensions=n),t}fromJSON(e,t){if(super.fromJSON(e,t),e.uniforms!==void 0)for(const n in e.uniforms){const s=e.uniforms[n];switch(this.uniforms[n]={},s.type){case"t":this.uniforms[n].value=t[s.value]||null;break;case"c":this.uniforms[n].value=new je().setHex(s.value);break;case"v2":this.uniforms[n].value=new Xe().fromArray(s.value);break;case"v3":this.uniforms[n].value=new z().fromArray(s.value);break;case"v4":this.uniforms[n].value=new pt().fromArray(s.value);break;case"m3":this.uniforms[n].value=new Ne().fromArray(s.value);break;case"m4":this.uniforms[n].value=new mt().fromArray(s.value);break;default:this.uniforms[n].value=s.value}}if(e.defines!==void 0&&(this.defines=e.defines),e.vertexShader!==void 0&&(this.vertexShader=e.vertexShader),e.fragmentShader!==void 0&&(this.fragmentShader=e.fragmentShader),e.glslVersion!==void 0&&(this.glslVersion=e.glslVersion),e.extensions!==void 0)for(const n in e.extensions)this.extensions[n]=e.extensions[n];return e.lights!==void 0&&(this.lights=e.lights),e.clipping!==void 0&&(this.clipping=e.clipping),this}}class Xc extends jn{constructor(e){super(e),this.isRawShaderMaterial=!0,this.type="RawShaderMaterial"}}class rn extends ki{constructor(e){super(),this.isMeshStandardMaterial=!0,this.type="MeshStandardMaterial",this.defines={STANDARD:""},this.color=new je(16777215),this.roughness=1,this.metalness=0,this.map=null,this.lightMap=null,this.lightMapIntensity=1,this.aoMap=null,this.aoMapIntensity=1,this.emissive=new je(0),this.emissiveIntensity=1,this.emissiveMap=null,this.bumpMap=null,this.bumpScale=1,this.normalMap=null,this.normalMapType=Kr,this.normalScale=new Xe(1,1),this.displacementMap=null,this.displacementScale=1,this.displacementBias=0,this.roughnessMap=null,this.metalnessMap=null,this.alphaMap=null,this.envMap=null,this.envMapRotation=new Gn,this.envMapIntensity=1,this.wireframe=!1,this.wireframeLinewidth=1,this.wireframeLinecap="round",this.wireframeLinejoin="round",this.flatShading=!1,this.fog=!0,this.setValues(e)}copy(e){return super.copy(e),this.defines={STANDARD:""},this.color.copy(e.color),this.roughness=e.roughness,this.metalness=e.metalness,this.map=e.map,this.lightMap=e.lightMap,this.lightMapIntensity=e.lightMapIntensity,this.aoMap=e.aoMap,this.aoMapIntensity=e.aoMapIntensity,this.emissive.copy(e.emissive),this.emissiveMap=e.emissiveMap,this.emissiveIntensity=e.emissiveIntensity,this.bumpMap=e.bumpMap,this.bumpScale=e.bumpScale,this.normalMap=e.normalMap,this.normalMapType=e.normalMapType,this.normalScale.copy(e.normalScale),this.displacementMap=e.displacementMap,this.displacementScale=e.displacementScale,this.displacementBias=e.displacementBias,this.roughnessMap=e.roughnessMap,this.metalnessMap=e.metalnessMap,this.alphaMap=e.alphaMap,this.envMap=e.envMap,this.envMapRotation.copy(e.envMapRotation),this.envMapIntensity=e.envMapIntensity,this.wireframe=e.wireframe,this.wireframeLinewidth=e.wireframeLinewidth,this.wireframeLinecap=e.wireframeLinecap,this.wireframeLinejoin=e.wireframeLinejoin,this.flatShading=e.flatShading,this.fog=e.fog,this}}class qc extends ki{constructor(e){super(),this.isMeshDepthMaterial=!0,this.type="MeshDepthMaterial",this.depthPacking=jl,this.map=null,this.alphaMap=null,this.displacementMap=null,this.displacementScale=1,this.displacementBias=0,this.wireframe=!1,this.wireframeLinewidth=1,this.setValues(e)}copy(e){return super.copy(e),this.depthPacking=e.depthPacking,this.map=e.map,this.alphaMap=e.alphaMap,this.displacementMap=e.displacementMap,this.displacementScale=e.displacementScale,this.displacementBias=e.displacementBias,this.wireframe=e.wireframe,this.wireframeLinewidth=e.wireframeLinewidth,this}}class Yc extends ki{constructor(e){super(),this.isMeshDistanceMaterial=!0,this.type="MeshDistanceMaterial",this.map=null,this.alphaMap=null,this.displacementMap=null,this.displacementScale=1,this.displacementBias=0,this.setValues(e)}copy(e){return super.copy(e),this.map=e.map,this.alphaMap=e.alphaMap,this.displacementMap=e.displacementMap,this.displacementScale=e.displacementScale,this.displacementBias=e.displacementBias,this}}class fa extends Ut{static registerNode(e){this.prototype._lightNode=e}constructor(e,t=1){super(),this.isLight=!0,this.type="Light",this.color=new je(e),this.intensity=t}copy(e,t){return super.copy(e,t),this.color.copy(e.color),this.intensity=e.intensity,this}toJSON(e){const t=super.toJSON(e);return t.object.color=this.color.getHex(),t.object.intensity=this.intensity,t}}const ar=new mt,ja=new z,eo=new z;class rl{constructor(e){this.camera=e,this.intensity=1,this.bias=0,this.biasNode=null,this.normalBias=0,this.radius=1,this.blurSamples=8,this.mapSize=new Xe(512,512),this.mapType=Zt,this.map=null,this.mapPass=null,this.matrix=new mt,this.autoUpdate=!0,this.needsUpdate=!1,this._frustum=new Fi,this._frameExtents=new Xe(1,1),this._viewportCount=1,this._viewports=[new pt(0,0,1,1)]}getViewportCount(){return this._viewportCount}getCamera(){return this.camera}getFrustum(){return this._frustum}updateMatrices(e){const t=this.camera;ja.setFromMatrixPosition(e.matrixWorld),t.position.copy(ja),eo.setFromMatrixPosition(e.target.matrixWorld),t.lookAt(eo),t.updateMatrixWorld(),this._updateMatrix(t,this.matrix,this._frustum)}_updateMatrix(e,t,n,s){ar.multiplyMatrices(e.projectionMatrix,e.matrixWorldInverse),n.setFromProjectionMatrix(ar,e.coordinateSystem,e.reversedDepth);const r=this._frameExtents,a=s?s.z/r.x:1,o=s?s.w/r.y:1,d=s?s.x/r.x:0,c=s?s.y/r.y:0;e.coordinateSystem===Ui||e.reversedDepth?t.set(.5*a,0,0,.5*a+d,0,.5*o,0,.5*o+c,0,0,1,0,0,0,0,1):t.set(.5*a,0,0,.5*a+d,0,.5*o,0,.5*o+c,0,0,.5,.5,0,0,0,1),t.multiply(ar)}getViewport(e){return this._viewports[e]}getFrameExtents(){return this._frameExtents}dispose(){this.map&&this.map.dispose(),this.mapPass&&this.mapPass.dispose()}copy(e){return this.camera=e.camera.clone(),this.intensity=e.intensity,this.bias=e.bias,this.radius=e.radius,this.autoUpdate=e.autoUpdate,this.needsUpdate=e.needsUpdate,this.normalBias=e.normalBias,this.blurSamples=e.blurSamples,this.mapSize.copy(e.mapSize),this.mapType=e.mapType,this.biasNode=e.biasNode,this}clone(){return new this.constructor().copy(this)}toJSON(){const e={};return e.intensity=this.intensity,e.bias=this.bias,e.normalBias=this.normalBias,e.radius=this.radius,e.blurSamples=this.blurSamples,e.mapSize=this.mapSize.toArray(),e.mapType=this.mapType,e.camera=this.camera.toJSON(!1).object,delete e.camera.matrix,e}}const os=new z,ls=new Mi,dn=new z;class al extends Ut{constructor(){super(),this.isCamera=!0,this.type="Camera",this.matrixWorldInverse=new mt,this.projectionMatrix=new mt,this.projectionMatrixInverse=new mt,this.coordinateSystem=ln,this._reversedDepth=!1}get reversedDepth(){return this._reversedDepth}copy(e,t){return super.copy(e,t),this.matrixWorldInverse.copy(e.matrixWorldInverse),this.projectionMatrix.copy(e.projectionMatrix),this.projectionMatrixInverse.copy(e.projectionMatrixInverse),this.coordinateSystem=e.coordinateSystem,this}getWorldDirection(e){return super.getWorldDirection(e).negate()}updateMatrixWorld(e){super.updateMatrixWorld(e),this.matrixWorld.decompose(os,ls,dn),dn.x===1&&dn.y===1&&dn.z===1?this.matrixWorldInverse.copy(this.matrixWorld).invert():this.matrixWorldInverse.compose(os,ls,dn.set(1,1,1)).invert()}updateWorldMatrix(e,t,n=!1){super.updateWorldMatrix(e,t,n),this.matrixWorld.decompose(os,ls,dn),dn.x===1&&dn.y===1&&dn.z===1?this.matrixWorldInverse.copy(this.matrixWorld).invert():this.matrixWorldInverse.compose(os,ls,dn.set(1,1,1)).invert()}clone(){return new this.constructor().copy(this)}}const Nn=new z,to=new Xe,no=new Xe;class Qt extends al{constructor(e=50,t=1,n=.1,s=2e3){super(),this.isPerspectiveCamera=!0,this.type="PerspectiveCamera",this.fov=e,this.zoom=1,this.near=n,this.far=s,this.focus=10,this.aspect=t,this.view=null,this.filmGauge=35,this.filmOffset=0,this.updateProjectionMatrix()}copy(e,t){return super.copy(e,t),this.fov=e.fov,this.zoom=e.zoom,this.near=e.near,this.far=e.far,this.focus=e.focus,this.aspect=e.aspect,this.view=e.view===null?null:Object.assign({},e.view),this.filmGauge=e.filmGauge,this.filmOffset=e.filmOffset,this}setFocalLength(e){const t=.5*this.getFilmHeight()/e;this.fov=Zr*2*Math.atan(t),this.updateProjectionMatrix()}getFocalLength(){const e=Math.tan(Bs*.5*this.fov);return .5*this.getFilmHeight()/e}getEffectiveFOV(){return Zr*2*Math.atan(Math.tan(Bs*.5*this.fov)/this.zoom)}getFilmWidth(){return this.filmGauge*Math.min(this.aspect,1)}getFilmHeight(){return this.filmGauge/Math.max(this.aspect,1)}getViewBounds(e,t,n){Nn.set(-1,-1,.5).applyMatrix4(this.projectionMatrixInverse),t.set(Nn.x,Nn.y).multiplyScalar(-e/Nn.z),Nn.set(1,1,.5).applyMatrix4(this.projectionMatrixInverse),n.set(Nn.x,Nn.y).multiplyScalar(-e/Nn.z)}getViewSize(e,t){return this.getViewBounds(e,to,no),t.subVectors(no,to)}setViewOffset(e,t,n,s,r,a){this.aspect=e/t,this.view===null&&(this.view={enabled:!0,fullWidth:1,fullHeight:1,offsetX:0,offsetY:0,width:1,height:1}),this.view.enabled=!0,this.view.fullWidth=e,this.view.fullHeight=t,this.view.offsetX=n,this.view.offsetY=s,this.view.width=r,this.view.height=a,this.updateProjectionMatrix()}clearViewOffset(){this.view!==null&&(this.view.enabled=!1),this.updateProjectionMatrix()}updateProjectionMatrix(){const e=this.near;let t=e*Math.tan(Bs*.5*this.fov)/this.zoom,n=2*t,s=this.aspect*n,r=-.5*s;const a=this.view;if(this.view!==null&&this.view.enabled){const d=a.fullWidth,c=a.fullHeight;r+=a.offsetX*s/d,t-=a.offsetY*n/c,s*=a.width/d,n*=a.height/c}const o=this.filmOffset;o!==0&&(r+=e*o/this.getFilmWidth()),this.projectionMatrix.makePerspective(r,r+s,t,t-n,e,this.far,this.coordinateSystem,this.reversedDepth),this.projectionMatrixInverse.copy(this.projectionMatrix).invert()}toJSON(e){const t=super.toJSON(e);return t.object.fov=this.fov,t.object.zoom=this.zoom,t.object.near=this.near,t.object.far=this.far,t.object.focus=this.focus,t.object.aspect=this.aspect,this.view!==null&&(t.object.view=Object.assign({},this.view)),t.object.filmGauge=this.filmGauge,t.object.filmOffset=this.filmOffset,t}}class Kc extends rl{constructor(){super(new Qt(90,1,.5,500)),this.isPointLightShadow=!0}}class Zc extends fa{constructor(e,t,n=0,s=2){super(e,t),this.isPointLight=!0,this.type="PointLight",this.distance=n,this.decay=s,this.shadow=new Kc}get power(){return this.intensity*4*Math.PI}set power(e){this.intensity=e/(4*Math.PI)}dispose(){super.dispose(),this.shadow.dispose()}copy(e,t){return super.copy(e,t),this.distance=e.distance,this.decay=e.decay,this.shadow=e.shadow.clone(),this}toJSON(e){const t=super.toJSON(e);return t.object.distance=this.distance,t.object.decay=this.decay,t.object.shadow=this.shadow.toJSON(),t}}class ol extends al{constructor(e=-1,t=1,n=1,s=-1,r=.1,a=2e3){super(),this.isOrthographicCamera=!0,this.type="OrthographicCamera",this.zoom=1,this.view=null,this.left=e,this.right=t,this.top=n,this.bottom=s,this.near=r,this.far=a,this.updateProjectionMatrix()}copy(e,t){return super.copy(e,t),this.left=e.left,this.right=e.right,this.top=e.top,this.bottom=e.bottom,this.near=e.near,this.far=e.far,this.zoom=e.zoom,this.view=e.view===null?null:Object.assign({},e.view),this}setViewOffset(e,t,n,s,r,a){this.view===null&&(this.view={enabled:!0,fullWidth:1,fullHeight:1,offsetX:0,offsetY:0,width:1,height:1}),this.view.enabled=!0,this.view.fullWidth=e,this.view.fullHeight=t,this.view.offsetX=n,this.view.offsetY=s,this.view.width=r,this.view.height=a,this.updateProjectionMatrix()}clearViewOffset(){this.view!==null&&(this.view.enabled=!1),this.updateProjectionMatrix()}updateProjectionMatrix(){const e=(this.right-this.left)/(2*this.zoom),t=(this.top-this.bottom)/(2*this.zoom),n=(this.right+this.left)/2,s=(this.top+this.bottom)/2;let r=n-e,a=n+e,o=s+t,d=s-t;if(this.view!==null&&this.view.enabled){const c=(this.right-this.left)/this.view.fullWidth/this.zoom,m=(this.top-this.bottom)/this.view.fullHeight/this.zoom;r+=c*this.view.offsetX,a=r+c*this.view.width,o-=m*this.view.offsetY,d=o-m*this.view.height}this.projectionMatrix.makeOrthographic(r,a,o,d,this.near,this.far,this.coordinateSystem,this.reversedDepth),this.projectionMatrixInverse.copy(this.projectionMatrix).invert()}toJSON(e){const t=super.toJSON(e);return t.object.zoom=this.zoom,t.object.left=this.left,t.object.right=this.right,t.object.top=this.top,t.object.bottom=this.bottom,t.object.near=this.near,t.object.far=this.far,this.view!==null&&(t.object.view=Object.assign({},this.view)),t}}class $c extends rl{constructor(){super(new ol(-5,5,5,-5,.5,500)),this.isDirectionalLightShadow=!0}}class io extends fa{constructor(e,t){super(e,t),this.isDirectionalLight=!0,this.type="DirectionalLight",this.position.copy(Ut.DEFAULT_UP),this.updateMatrix(),this.target=new Ut,this.shadow=new $c}dispose(){super.dispose(),this.shadow.dispose()}copy(e){return super.copy(e),this.target=e.target.clone(),this.shadow=e.shadow.clone(),this}toJSON(e){const t=super.toJSON(e);return t.object.shadow=this.shadow.toJSON(),t.object.target=this.target.uuid,t}}class Jc extends fa{constructor(e,t){super(e,t),this.isAmbientLight=!0,this.type="AmbientLight"}}const fi=-90,hi=1;class $r extends Ut{constructor(e,t,n){super(),this.type="CubeCamera",this.renderTarget=n,this.coordinateSystem=null,this.activeMipmapLevel=0;const s=new Qt(fi,hi,e,t);s.layers=this.layers,this.add(s);const r=new Qt(fi,hi,e,t);r.layers=this.layers,this.add(r);const a=new Qt(fi,hi,e,t);a.layers=this.layers,this.add(a);const o=new Qt(fi,hi,e,t);o.layers=this.layers,this.add(o);const d=new Qt(fi,hi,e,t);d.layers=this.layers,this.add(d);const c=new Qt(fi,hi,e,t);c.layers=this.layers,this.add(c)}updateCoordinateSystem(){const e=this.coordinateSystem,t=this.children.concat(),[n,s,r,a,o,d]=t;for(const c of t)this.remove(c);if(e===ln)n.up.set(0,1,0),n.lookAt(1,0,0),s.up.set(0,1,0),s.lookAt(-1,0,0),r.up.set(0,0,-1),r.lookAt(0,1,0),a.up.set(0,0,1),a.lookAt(0,-1,0),o.up.set(0,1,0),o.lookAt(0,0,1),d.up.set(0,1,0),d.lookAt(0,0,-1);else if(e===Ui)n.up.set(0,-1,0),n.lookAt(-1,0,0),s.up.set(0,-1,0),s.lookAt(1,0,0),r.up.set(0,0,1),r.lookAt(0,1,0),a.up.set(0,0,-1),a.lookAt(0,-1,0),o.up.set(0,-1,0),o.lookAt(0,0,1),d.up.set(0,-1,0),d.lookAt(0,0,-1);else throw new Error("THREE.CubeCamera.updateCoordinateSystem(): Invalid coordinate system: "+e);for(const c of t)this.add(c),c.updateMatrixWorld()}update(e,t){this.parent===null&&this.updateMatrixWorld();const{renderTarget:n,activeMipmapLevel:s}=this;this.coordinateSystem!==e.coordinateSystem&&(this.coordinateSystem=e.coordinateSystem,this.updateCoordinateSystem());const[r,a,o,d,c,m]=this.children,x=e.getRenderTarget(),f=e.getActiveCubeFace(),g=e.getActiveMipmapLevel(),l=e.xr.enabled;e.xr.enabled=!1;const T=n.texture.mipmapsAutoUpdate;n.texture.mipmapsAutoUpdate=!1;let p=!1;e.isWebGLRenderer===!0?p=e.state.buffers.depth.getReversed():p=e.reversedDepthBuffer,e.setRenderTarget(n,0,s),p&&e.autoClear===!1&&e.clearDepth(),e.render(t,r),e.setRenderTarget(n,1,s),p&&e.autoClear===!1&&e.clearDepth(),e.render(t,a),e.setRenderTarget(n,2,s),p&&e.autoClear===!1&&e.clearDepth(),e.render(t,o),e.setRenderTarget(n,3,s),p&&e.autoClear===!1&&e.clearDepth(),e.render(t,d),e.setRenderTarget(n,4,s),p&&e.autoClear===!1&&e.clearDepth(),e.render(t,c),n.texture.mipmapsAutoUpdate=T,e.setRenderTarget(n,5,s),p&&e.autoClear===!1&&e.clearDepth(),e.render(t,m),e.setRenderTarget(x,f,g),e.xr.enabled=l,n.texture.needsPMREMUpdate=!0}}class Qc extends Qt{constructor(e=[]){super(),this.isArrayCamera=!0,this.isMultiViewCamera=!1,this.cameras=e,this.perCameraFrustumCulled=!1}}class jc{constructor(e=!0){this.autoStart=e,this.startTime=0,this.oldTime=0,this.elapsedTime=0,this.running=!1,De("Clock: This module has been deprecated. Please use THREE.Timer instead.")}start(){this.startTime=performance.now(),this.oldTime=this.startTime,this.elapsedTime=0,this.running=!0}stop(){this.getElapsedTime(),this.running=!1,this.autoStart=!1}getElapsedTime(){return this.getDelta(),this.elapsedTime}getDelta(){let e=0;if(this.autoStart&&!this.running)return this.start(),0;if(this.running){const t=performance.now();e=(t-this.oldTime)/1e3,this.oldTime=t,this.elapsedTime+=e}return e}}function so(i,e,t,n){const s=ed(n);switch(t){case Vo:return i*e;case Xo:return i*e/s.components*s.byteLength;case ia:return i*e/s.components*s.byteLength;case Qn:return i*e*2/s.components*s.byteLength;case sa:return i*e*2/s.components*s.byteLength;case Wo:return i*e*3/s.components*s.byteLength;case pn:return i*e*4/s.components*s.byteLength;case ra:return i*e*4/s.components*s.byteLength;case hs:case ps:return Math.floor((i+3)/4)*Math.floor((e+3)/4)*8;case ms:case gs:return Math.floor((i+3)/4)*Math.floor((e+3)/4)*16;case vr:case Mr:return Math.max(i,16)*Math.max(e,8)/4;case _r:case Sr:return Math.max(i,8)*Math.max(e,8)/2;case br:case yr:case Tr:case wr:return Math.floor((i+3)/4)*Math.floor((e+3)/4)*8;case Er:case vs:case Ar:return Math.floor((i+3)/4)*Math.floor((e+3)/4)*16;case Rr:return Math.floor((i+3)/4)*Math.floor((e+3)/4)*16;case Cr:return Math.floor((i+4)/5)*Math.floor((e+3)/4)*16;case Pr:return Math.floor((i+4)/5)*Math.floor((e+4)/5)*16;case Lr:return Math.floor((i+5)/6)*Math.floor((e+4)/5)*16;case Dr:return Math.floor((i+5)/6)*Math.floor((e+5)/6)*16;case Ir:return Math.floor((i+7)/8)*Math.floor((e+4)/5)*16;case Nr:return Math.floor((i+7)/8)*Math.floor((e+5)/6)*16;case Ur:return Math.floor((i+7)/8)*Math.floor((e+7)/8)*16;case Fr:return Math.floor((i+9)/10)*Math.floor((e+4)/5)*16;case Or:return Math.floor((i+9)/10)*Math.floor((e+5)/6)*16;case Br:return Math.floor((i+9)/10)*Math.floor((e+7)/8)*16;case kr:return Math.floor((i+9)/10)*Math.floor((e+9)/10)*16;case zr:return Math.floor((i+11)/12)*Math.floor((e+9)/10)*16;case Gr:return Math.floor((i+11)/12)*Math.floor((e+11)/12)*16;case Hr:case Vr:case Wr:return Math.ceil(i/4)*Math.ceil(e/4)*16;case Xr:case qr:return Math.ceil(i/4)*Math.ceil(e/4)*8;case Ss:case Yr:return Math.ceil(i/4)*Math.ceil(e/4)*16}throw new Error(`Unable to determine texture byte length for ${t} format.`)}function ed(i){switch(i){case Zt:case ko:return{byteLength:1,components:1};case Ni:case zo:case xn:return{byteLength:2,components:1};case ta:case na:return{byteLength:2,components:4};case gn:case ea:case hn:return{byteLength:4,components:1};case Go:case Ho:return{byteLength:4,components:3}}throw new Error(`THREE.TextureUtils: Unknown texture type ${i}.`)}typeof __THREE_DEVTOOLS__<"u"&&__THREE_DEVTOOLS__.dispatchEvent(new CustomEvent("register",{detail:{revision:Qr}}));typeof window<"u"&&(window.__THREE__?De("WARNING: Multiple instances of Three.js being imported."):window.__THREE__=Qr);/**
 * @license
 * Copyright 2010-2026 Three.js Authors
 * SPDX-License-Identifier: MIT
 */function ll(){let i=null,e=!1,t=null,n=null;function s(r,a){n=i.requestAnimationFrame(s),t(r,a)}return{start:function(){e!==!0&&t!==null&&i!==null&&(n=i.requestAnimationFrame(s),e=!0)},stop:function(){i!==null&&i.cancelAnimationFrame(n),e=!1},setAnimationLoop:function(r){t=r},setContext:function(r){i=r}}}function td(i){const e=new WeakMap;function t(o,d){const c=o.array,m=o.usage,x=c.byteLength,f=i.createBuffer();i.bindBuffer(d,f),i.bufferData(d,c,m),o.onUploadCallback();let g;if(c instanceof Float32Array)g=i.FLOAT;else if(typeof Float16Array<"u"&&c instanceof Float16Array)g=i.HALF_FLOAT;else if(c instanceof Uint16Array)o.isFloat16BufferAttribute?g=i.HALF_FLOAT:g=i.UNSIGNED_SHORT;else if(c instanceof Int16Array)g=i.SHORT;else if(c instanceof Uint32Array)g=i.UNSIGNED_INT;else if(c instanceof Int32Array)g=i.INT;else if(c instanceof Int8Array)g=i.BYTE;else if(c instanceof Uint8Array)g=i.UNSIGNED_BYTE;else if(c instanceof Uint8ClampedArray)g=i.UNSIGNED_BYTE;else throw new Error("THREE.WebGLAttributes: Unsupported buffer data format: "+c);return{buffer:f,type:g,bytesPerElement:c.BYTES_PER_ELEMENT,version:o.version,size:x}}function n(o,d,c){const m=d.array,x=d.updateRanges;if(i.bindBuffer(c,o),x.length===0)i.bufferSubData(c,0,m);else{x.sort((g,l)=>g.start-l.start);let f=0;for(let g=1;g<x.length;g++){const l=x[f],T=x[g];T.start<=l.start+l.count+1?l.count=Math.max(l.count,T.start+T.count-l.start):(++f,x[f]=T)}x.length=f+1;for(let g=0,l=x.length;g<l;g++){const T=x[g];i.bufferSubData(c,T.start*m.BYTES_PER_ELEMENT,m,T.start,T.count)}d.clearUpdateRanges()}d.onUploadCallback()}function s(o){return o.isInterleavedBufferAttribute&&(o=o.data),e.get(o)}function r(o){o.isInterleavedBufferAttribute&&(o=o.data);const d=e.get(o);d&&(i.deleteBuffer(d.buffer),e.delete(o))}function a(o,d){if(o.isInterleavedBufferAttribute&&(o=o.data),o.isGLBufferAttribute){const m=e.get(o);(!m||m.version<o.version)&&e.set(o,{buffer:o.buffer,type:o.type,bytesPerElement:o.elementSize,version:o.version});return}const c=e.get(o);if(c===void 0)e.set(o,t(o,d));else if(c.version<o.version){if(c.size!==o.array.byteLength)throw new Error("THREE.WebGLAttributes: The size of the buffer attribute's array buffer does not match the original size. Resizing buffer attributes is not supported.");n(c.buffer,o,d),c.version=o.version}}return{get:s,remove:r,update:a}}var nd=`#ifdef USE_ALPHAHASH
	if ( diffuseColor.a < getAlphaHashThreshold( vPosition ) ) discard;
#endif`,id=`#ifdef USE_ALPHAHASH
	const float ALPHA_HASH_SCALE = 0.05;
	float hash2D( vec2 value ) {
		return fract( 1.0e4 * sin( 17.0 * value.x + 0.1 * value.y ) * ( 0.1 + abs( sin( 13.0 * value.y + value.x ) ) ) );
	}
	float hash3D( vec3 value ) {
		return hash2D( vec2( hash2D( value.xy ), value.z ) );
	}
	float getAlphaHashThreshold( vec3 position ) {
		float maxDeriv = max(
			length( dFdx( position.xyz ) ),
			length( dFdy( position.xyz ) )
		);
		float pixScale = 1.0 / ( ALPHA_HASH_SCALE * maxDeriv );
		vec2 pixScales = vec2(
			exp2( floor( log2( pixScale ) ) ),
			exp2( ceil( log2( pixScale ) ) )
		);
		vec2 alpha = vec2(
			hash3D( floor( pixScales.x * position.xyz ) ),
			hash3D( floor( pixScales.y * position.xyz ) )
		);
		float lerpFactor = fract( log2( pixScale ) );
		float x = ( 1.0 - lerpFactor ) * alpha.x + lerpFactor * alpha.y;
		float a = min( lerpFactor, 1.0 - lerpFactor );
		vec3 cases = vec3(
			x * x / ( 2.0 * a * ( 1.0 - a ) ),
			( x - 0.5 * a ) / ( 1.0 - a ),
			1.0 - ( ( 1.0 - x ) * ( 1.0 - x ) / ( 2.0 * a * ( 1.0 - a ) ) )
		);
		float threshold = ( x < ( 1.0 - a ) )
			? ( ( x < a ) ? cases.x : cases.y )
			: cases.z;
		return clamp( threshold , 1.0e-6, 1.0 );
	}
#endif`,sd=`#ifdef USE_ALPHAMAP
	diffuseColor.a *= texture2D( alphaMap, vAlphaMapUv ).g;
#endif`,rd=`#ifdef USE_ALPHAMAP
	uniform sampler2D alphaMap;
#endif`,ad=`#ifdef USE_ALPHATEST
	#ifdef ALPHA_TO_COVERAGE
	diffuseColor.a = smoothstep( alphaTest, alphaTest + fwidth( diffuseColor.a ), diffuseColor.a );
	if ( diffuseColor.a == 0.0 ) discard;
	#else
	if ( diffuseColor.a < alphaTest ) discard;
	#endif
#endif`,od=`#ifdef USE_ALPHATEST
	uniform float alphaTest;
#endif`,ld=`#ifdef USE_AOMAP
	float ambientOcclusion = ( texture2D( aoMap, vAoMapUv ).r - 1.0 ) * aoMapIntensity + 1.0;
	reflectedLight.indirectDiffuse *= ambientOcclusion;
	#if defined( USE_CLEARCOAT ) 
		clearcoatSpecularIndirect *= ambientOcclusion;
	#endif
	#if defined( USE_SHEEN ) 
		sheenSpecularIndirect *= ambientOcclusion;
	#endif
	#if defined( USE_ENVMAP ) && defined( STANDARD )
		float dotNV = saturate( dot( geometryNormal, geometryViewDir ) );
		reflectedLight.indirectSpecular *= computeSpecularOcclusion( dotNV, ambientOcclusion, material.roughness );
	#endif
#endif`,cd=`#ifdef USE_AOMAP
	uniform sampler2D aoMap;
	uniform float aoMapIntensity;
#endif`,dd=`#ifdef USE_BATCHING
	#if ! defined( GL_ANGLE_multi_draw )
	#define gl_DrawID _gl_DrawID
	uniform int _gl_DrawID;
	#endif
	uniform highp sampler2D batchingTexture;
	uniform highp usampler2D batchingIdTexture;
	mat4 getBatchingMatrix( const in float i ) {
		int size = textureSize( batchingTexture, 0 ).x;
		int j = int( i ) * 4;
		int x = j % size;
		int y = j / size;
		vec4 v1 = texelFetch( batchingTexture, ivec2( x, y ), 0 );
		vec4 v2 = texelFetch( batchingTexture, ivec2( x + 1, y ), 0 );
		vec4 v3 = texelFetch( batchingTexture, ivec2( x + 2, y ), 0 );
		vec4 v4 = texelFetch( batchingTexture, ivec2( x + 3, y ), 0 );
		return mat4( v1, v2, v3, v4 );
	}
	float getIndirectIndex( const in int i ) {
		int size = textureSize( batchingIdTexture, 0 ).x;
		int x = i % size;
		int y = i / size;
		return float( texelFetch( batchingIdTexture, ivec2( x, y ), 0 ).r );
	}
#endif
#ifdef USE_BATCHING_COLOR
	uniform sampler2D batchingColorTexture;
	vec4 getBatchingColor( const in float i ) {
		int size = textureSize( batchingColorTexture, 0 ).x;
		int j = int( i );
		int x = j % size;
		int y = j / size;
		return texelFetch( batchingColorTexture, ivec2( x, y ), 0 );
	}
#endif`,ud=`#ifdef USE_BATCHING
	mat4 batchingMatrix = getBatchingMatrix( getIndirectIndex( gl_DrawID ) );
#endif`,fd=`vec3 transformed = vec3( position );
#ifdef USE_ALPHAHASH
	vPosition = vec3( position );
#endif`,hd=`vec3 objectNormal = vec3( normal );
#ifdef USE_TANGENT
	vec3 objectTangent = vec3( tangent.xyz );
#endif`,pd=`float G_BlinnPhong_Implicit( ) {
	return 0.25;
}
float D_BlinnPhong( const in float shininess, const in float dotNH ) {
	return RECIPROCAL_PI * ( shininess * 0.5 + 1.0 ) * pow( dotNH, shininess );
}
vec3 BRDF_BlinnPhong( const in vec3 lightDir, const in vec3 viewDir, const in vec3 normal, const in vec3 specularColor, const in float shininess ) {
	vec3 halfDir = normalize( lightDir + viewDir );
	float dotNH = saturate( dot( normal, halfDir ) );
	float dotVH = saturate( dot( viewDir, halfDir ) );
	vec3 F = F_Schlick( specularColor, 1.0, dotVH );
	float G = G_BlinnPhong_Implicit( );
	float D = D_BlinnPhong( shininess, dotNH );
	return F * ( G * D );
} // validated`,md=`#ifdef USE_IRIDESCENCE
	const mat3 XYZ_TO_REC709 = mat3(
		 3.2404542, -0.9692660,  0.0556434,
		-1.5371385,  1.8760108, -0.2040259,
		-0.4985314,  0.0415560,  1.0572252
	);
	vec3 Fresnel0ToIor( vec3 fresnel0 ) {
		vec3 sqrtF0 = sqrt( fresnel0 );
		return ( vec3( 1.0 ) + sqrtF0 ) / ( vec3( 1.0 ) - sqrtF0 );
	}
	vec3 IorToFresnel0( vec3 transmittedIor, float incidentIor ) {
		return pow2( ( transmittedIor - vec3( incidentIor ) ) / ( transmittedIor + vec3( incidentIor ) ) );
	}
	float IorToFresnel0( float transmittedIor, float incidentIor ) {
		return pow2( ( transmittedIor - incidentIor ) / ( transmittedIor + incidentIor ));
	}
	vec3 evalSensitivity( float OPD, vec3 shift ) {
		float phase = 2.0 * PI * OPD * 1.0e-9;
		vec3 val = vec3( 5.4856e-13, 4.4201e-13, 5.2481e-13 );
		vec3 pos = vec3( 1.6810e+06, 1.7953e+06, 2.2084e+06 );
		vec3 var = vec3( 4.3278e+09, 9.3046e+09, 6.6121e+09 );
		vec3 xyz = val * sqrt( 2.0 * PI * var ) * cos( pos * phase + shift ) * exp( - pow2( phase ) * var );
		xyz.x += 9.7470e-14 * sqrt( 2.0 * PI * 4.5282e+09 ) * cos( 2.2399e+06 * phase + shift[ 0 ] ) * exp( - 4.5282e+09 * pow2( phase ) );
		xyz /= 1.0685e-7;
		vec3 rgb = XYZ_TO_REC709 * xyz;
		return rgb;
	}
	vec3 evalIridescence( float outsideIOR, float eta2, float cosTheta1, float thinFilmThickness, vec3 baseF0 ) {
		vec3 I;
		float iridescenceIOR = mix( outsideIOR, eta2, smoothstep( 0.0, 0.03, thinFilmThickness ) );
		float sinTheta2Sq = pow2( outsideIOR / iridescenceIOR ) * ( 1.0 - pow2( cosTheta1 ) );
		float cosTheta2Sq = 1.0 - sinTheta2Sq;
		if ( cosTheta2Sq < 0.0 ) {
			return vec3( 1.0 );
		}
		float cosTheta2 = sqrt( cosTheta2Sq );
		float R0 = IorToFresnel0( iridescenceIOR, outsideIOR );
		float R12 = F_Schlick( R0, 1.0, cosTheta1 );
		float T121 = 1.0 - R12;
		float phi12 = 0.0;
		if ( iridescenceIOR < outsideIOR ) phi12 = PI;
		float phi21 = PI - phi12;
		vec3 baseIOR = Fresnel0ToIor( clamp( baseF0, 0.0, 0.9999 ) );		vec3 R1 = IorToFresnel0( baseIOR, iridescenceIOR );
		vec3 R23 = F_Schlick( R1, 1.0, cosTheta2 );
		vec3 phi23 = vec3( 0.0 );
		if ( baseIOR[ 0 ] < iridescenceIOR ) phi23[ 0 ] = PI;
		if ( baseIOR[ 1 ] < iridescenceIOR ) phi23[ 1 ] = PI;
		if ( baseIOR[ 2 ] < iridescenceIOR ) phi23[ 2 ] = PI;
		float OPD = 2.0 * iridescenceIOR * thinFilmThickness * cosTheta2;
		vec3 phi = vec3( phi21 ) + phi23;
		vec3 R123 = clamp( R12 * R23, 1e-5, 0.9999 );
		vec3 r123 = sqrt( R123 );
		vec3 Rs = pow2( T121 ) * R23 / ( vec3( 1.0 ) - R123 );
		vec3 C0 = R12 + Rs;
		I = C0;
		vec3 Cm = Rs - T121;
		for ( int m = 1; m <= 2; ++ m ) {
			Cm *= r123;
			vec3 Sm = 2.0 * evalSensitivity( float( m ) * OPD, float( m ) * phi );
			I += Cm * Sm;
		}
		return max( I, vec3( 0.0 ) );
	}
#endif`,gd=`#ifdef USE_BUMPMAP
	uniform sampler2D bumpMap;
	uniform float bumpScale;
	vec2 dHdxy_fwd() {
		vec2 dSTdx = dFdx( vBumpMapUv );
		vec2 dSTdy = dFdy( vBumpMapUv );
		float Hll = bumpScale * texture2D( bumpMap, vBumpMapUv ).x;
		float dBx = bumpScale * texture2D( bumpMap, vBumpMapUv + dSTdx ).x - Hll;
		float dBy = bumpScale * texture2D( bumpMap, vBumpMapUv + dSTdy ).x - Hll;
		return vec2( dBx, dBy );
	}
	vec3 perturbNormalArb( vec3 surf_pos, vec3 surf_norm, vec2 dHdxy, float faceDirection ) {
		vec3 vSigmaX = normalize( dFdx( surf_pos.xyz ) );
		vec3 vSigmaY = normalize( dFdy( surf_pos.xyz ) );
		vec3 vN = surf_norm;
		vec3 R1 = cross( vSigmaY, vN );
		vec3 R2 = cross( vN, vSigmaX );
		float fDet = dot( vSigmaX, R1 ) * faceDirection;
		vec3 vGrad = sign( fDet ) * ( dHdxy.x * R1 + dHdxy.y * R2 );
		return normalize( abs( fDet ) * surf_norm - vGrad );
	}
#endif`,xd=`#if NUM_CLIPPING_PLANES > 0
	vec4 plane;
	#ifdef ALPHA_TO_COVERAGE
		float distanceToPlane, distanceGradient;
		float clipOpacity = 1.0;
		#pragma unroll_loop_start
		for ( int i = 0; i < UNION_CLIPPING_PLANES; i ++ ) {
			plane = clippingPlanes[ i ];
			distanceToPlane = - dot( vClipPosition, plane.xyz ) + plane.w;
			distanceGradient = fwidth( distanceToPlane ) / 2.0;
			clipOpacity *= smoothstep( - distanceGradient, distanceGradient, distanceToPlane );
			if ( clipOpacity == 0.0 ) discard;
		}
		#pragma unroll_loop_end
		#if UNION_CLIPPING_PLANES < NUM_CLIPPING_PLANES
			float unionClipOpacity = 1.0;
			#pragma unroll_loop_start
			for ( int i = UNION_CLIPPING_PLANES; i < NUM_CLIPPING_PLANES; i ++ ) {
				plane = clippingPlanes[ i ];
				distanceToPlane = - dot( vClipPosition, plane.xyz ) + plane.w;
				distanceGradient = fwidth( distanceToPlane ) / 2.0;
				unionClipOpacity *= 1.0 - smoothstep( - distanceGradient, distanceGradient, distanceToPlane );
			}
			#pragma unroll_loop_end
			clipOpacity *= 1.0 - unionClipOpacity;
		#endif
		diffuseColor.a *= clipOpacity;
		if ( diffuseColor.a == 0.0 ) discard;
	#else
		#pragma unroll_loop_start
		for ( int i = 0; i < UNION_CLIPPING_PLANES; i ++ ) {
			plane = clippingPlanes[ i ];
			if ( dot( vClipPosition, plane.xyz ) > plane.w ) discard;
		}
		#pragma unroll_loop_end
		#if UNION_CLIPPING_PLANES < NUM_CLIPPING_PLANES
			bool clipped = true;
			#pragma unroll_loop_start
			for ( int i = UNION_CLIPPING_PLANES; i < NUM_CLIPPING_PLANES; i ++ ) {
				plane = clippingPlanes[ i ];
				clipped = ( dot( vClipPosition, plane.xyz ) > plane.w ) && clipped;
			}
			#pragma unroll_loop_end
			if ( clipped ) discard;
		#endif
	#endif
#endif`,_d=`#if NUM_CLIPPING_PLANES > 0
	varying vec3 vClipPosition;
	uniform vec4 clippingPlanes[ NUM_CLIPPING_PLANES ];
#endif`,vd=`#if NUM_CLIPPING_PLANES > 0
	varying vec3 vClipPosition;
#endif`,Sd=`#if NUM_CLIPPING_PLANES > 0
	vClipPosition = - mvPosition.xyz;
#endif`,Md=`#if defined( USE_COLOR ) || defined( USE_COLOR_ALPHA )
	diffuseColor *= vColor;
#endif`,bd=`#if defined( USE_COLOR ) || defined( USE_COLOR_ALPHA )
	varying vec4 vColor;
#endif`,yd=`#if defined( USE_COLOR ) || defined( USE_COLOR_ALPHA ) || defined( USE_INSTANCING_COLOR ) || defined( USE_BATCHING_COLOR )
	varying vec4 vColor;
#endif`,Ed=`#if defined( USE_COLOR ) || defined( USE_COLOR_ALPHA ) || defined( USE_INSTANCING_COLOR ) || defined( USE_BATCHING_COLOR )
	vColor = vec4( 1.0 );
#endif
#ifdef USE_COLOR_ALPHA
	vColor *= color;
#elif defined( USE_COLOR )
	vColor.rgb *= color;
#endif
#ifdef USE_INSTANCING_COLOR
	vColor.rgb *= instanceColor.rgb;
#endif
#ifdef USE_BATCHING_COLOR
	vColor *= getBatchingColor( getIndirectIndex( gl_DrawID ) );
#endif`,Td=`#define PI 3.141592653589793
#define PI2 6.283185307179586
#define PI_HALF 1.5707963267948966
#define RECIPROCAL_PI 0.3183098861837907
#define RECIPROCAL_PI2 0.15915494309189535
#define EPSILON 1e-6
#ifndef saturate
#define saturate( a ) clamp( a, 0.0, 1.0 )
#endif
#define whiteComplement( a ) ( 1.0 - saturate( a ) )
float pow2( const in float x ) { return x*x; }
vec3 pow2( const in vec3 x ) { return x*x; }
float pow3( const in float x ) { return x*x*x; }
float pow4( const in float x ) { float x2 = x*x; return x2*x2; }
float max3( const in vec3 v ) { return max( max( v.x, v.y ), v.z ); }
float average( const in vec3 v ) { return dot( v, vec3( 0.3333333 ) ); }
highp float rand( const in vec2 uv ) {
	const highp float a = 12.9898, b = 78.233, c = 43758.5453;
	highp float dt = dot( uv.xy, vec2( a,b ) ), sn = mod( dt, PI );
	return fract( sin( sn ) * c );
}
#ifdef HIGH_PRECISION
	float precisionSafeLength( vec3 v ) { return length( v ); }
#else
	float precisionSafeLength( vec3 v ) {
		float maxComponent = max3( abs( v ) );
		return length( v / maxComponent ) * maxComponent;
	}
#endif
struct IncidentLight {
	vec3 color;
	vec3 direction;
	bool visible;
};
struct ReflectedLight {
	vec3 directDiffuse;
	vec3 directSpecular;
	vec3 indirectDiffuse;
	vec3 indirectSpecular;
};
#ifdef USE_ALPHAHASH
	varying vec3 vPosition;
#endif
vec3 transformDirection( in vec3 dir, in mat4 matrix ) {
	return normalize( ( matrix * vec4( dir, 0.0 ) ).xyz );
}
#define inverseTransformDirection transformDirectionByInverseViewMatrix
vec3 transformNormalByInverseViewMatrix( in vec3 normal, in mat4 viewMatrix ) {
	return normalize( ( vec4( normal, 0.0 ) * viewMatrix ).xyz );
}
vec3 transformDirectionByInverseViewMatrix( in vec3 dir, in mat4 viewMatrix ) {
	return normalize( ( vec4( dir, 0.0 ) * viewMatrix ).xyz );
}
bool isPerspectiveMatrix( mat4 m ) {
	return m[ 2 ][ 3 ] == - 1.0;
}
vec2 equirectUv( in vec3 dir ) {
	float u = atan( dir.z, dir.x ) * RECIPROCAL_PI2 + 0.5;
	float v = asin( clamp( dir.y, - 1.0, 1.0 ) ) * RECIPROCAL_PI + 0.5;
	return vec2( u, v );
}
vec3 BRDF_Lambert( const in vec3 diffuseColor ) {
	return RECIPROCAL_PI * diffuseColor;
}
vec3 F_Schlick( const in vec3 f0, const in float f90, const in float dotVH ) {
	float fresnel = exp2( ( - 5.55473 * dotVH - 6.98316 ) * dotVH );
	return f0 * ( 1.0 - fresnel ) + ( f90 * fresnel );
}
float F_Schlick( const in float f0, const in float f90, const in float dotVH ) {
	float fresnel = exp2( ( - 5.55473 * dotVH - 6.98316 ) * dotVH );
	return f0 * ( 1.0 - fresnel ) + ( f90 * fresnel );
} // validated`,wd=`vec3 transformedNormal = objectNormal;
#ifdef USE_TANGENT
	vec3 transformedTangent = objectTangent;
#endif
#ifdef USE_BATCHING
	mat3 bm = mat3( batchingMatrix );
	transformedNormal /= vec3( dot( bm[ 0 ], bm[ 0 ] ), dot( bm[ 1 ], bm[ 1 ] ), dot( bm[ 2 ], bm[ 2 ] ) );
	transformedNormal = bm * transformedNormal;
	#ifdef USE_TANGENT
		transformedTangent = bm * transformedTangent;
	#endif
#endif
#ifdef USE_INSTANCING
	mat3 im = mat3( instanceMatrix );
	transformedNormal /= vec3( dot( im[ 0 ], im[ 0 ] ), dot( im[ 1 ], im[ 1 ] ), dot( im[ 2 ], im[ 2 ] ) );
	transformedNormal = im * transformedNormal;
	#ifdef USE_TANGENT
		transformedTangent = im * transformedTangent;
	#endif
#endif
transformedNormal = normalMatrix * transformedNormal;
#ifdef FLIP_SIDED
	transformedNormal = - transformedNormal;
#endif
#ifdef USE_TANGENT
	transformedTangent = ( modelViewMatrix * vec4( transformedTangent, 0.0 ) ).xyz;
#endif`,Ad=`#ifdef USE_DISPLACEMENTMAP
	uniform sampler2D displacementMap;
	uniform float displacementScale;
	uniform float displacementBias;
#endif`,Rd=`#ifdef USE_DISPLACEMENTMAP
	transformed += normalize( objectNormal ) * ( texture2D( displacementMap, vDisplacementMapUv ).x * displacementScale + displacementBias );
#endif`,Cd=`#ifdef USE_EMISSIVEMAP
	vec4 emissiveColor = texture2D( emissiveMap, vEmissiveMapUv );
	#ifdef DECODE_VIDEO_TEXTURE_EMISSIVE
		emissiveColor = sRGBTransferEOTF( emissiveColor );
	#endif
	totalEmissiveRadiance *= emissiveColor.rgb;
#endif`,Pd=`#ifdef USE_EMISSIVEMAP
	uniform sampler2D emissiveMap;
#endif`,Ld="gl_FragColor = linearToOutputTexel( gl_FragColor );",Dd=`vec4 LinearTransferOETF( in vec4 value ) {
	return value;
}
vec4 sRGBTransferEOTF( in vec4 value ) {
	return vec4( mix( pow( value.rgb * 0.9478672986 + vec3( 0.0521327014 ), vec3( 2.4 ) ), value.rgb * 0.0773993808, vec3( lessThanEqual( value.rgb, vec3( 0.04045 ) ) ) ), value.a );
}
vec4 sRGBTransferOETF( in vec4 value ) {
	return vec4( mix( pow( value.rgb, vec3( 0.41666 ) ) * 1.055 - vec3( 0.055 ), value.rgb * 12.92, vec3( lessThanEqual( value.rgb, vec3( 0.0031308 ) ) ) ), value.a );
}`,Id=`#ifdef USE_ENVMAP
	#ifdef ENV_WORLDPOS
		vec3 cameraToFrag;
		if ( isOrthographic ) {
			cameraToFrag = normalize( vec3( - viewMatrix[ 0 ][ 2 ], - viewMatrix[ 1 ][ 2 ], - viewMatrix[ 2 ][ 2 ] ) );
		} else {
			cameraToFrag = normalize( vWorldPosition - cameraPosition );
		}
		vec3 worldNormal = transformNormalByInverseViewMatrix( normal, viewMatrix );
		#ifdef ENVMAP_MODE_REFLECTION
			vec3 reflectVec = reflect( cameraToFrag, worldNormal );
		#else
			vec3 reflectVec = refract( cameraToFrag, worldNormal, refractionRatio );
		#endif
	#else
		vec3 reflectVec = vReflect;
	#endif
	#ifdef ENVMAP_TYPE_CUBE
		vec4 envColor = textureCube( envMap, envMapRotation * reflectVec );
		#ifdef ENVMAP_BLENDING_MULTIPLY
			outgoingLight = mix( outgoingLight, outgoingLight * envColor.xyz, specularStrength * reflectivity );
		#elif defined( ENVMAP_BLENDING_MIX )
			outgoingLight = mix( outgoingLight, envColor.xyz, specularStrength * reflectivity );
		#elif defined( ENVMAP_BLENDING_ADD )
			outgoingLight += envColor.xyz * specularStrength * reflectivity;
		#endif
	#endif
#endif`,Nd=`#ifdef USE_ENVMAP
	uniform float envMapIntensity;
	uniform mat3 envMapRotation;
	uniform samplerCube envMap;
	#ifdef ENVMAP_TYPE_PMREM
		float roughnessToMip( const in float roughness ) {
			float r = clamp( roughness, 0.0, 1.0 );
			return ENVMAP_MAX_LOD * r * ( 2.0 - r );
		}
	#endif
#endif`,Ud=`#ifdef USE_ENVMAP
	uniform float reflectivity;
	#if defined( USE_BUMPMAP ) || defined( USE_NORMALMAP ) || defined( PHONG ) || defined( LAMBERT )
		#define ENV_WORLDPOS
	#endif
	#ifdef ENV_WORLDPOS
		varying vec3 vWorldPosition;
		uniform float refractionRatio;
	#else
		varying vec3 vReflect;
	#endif
#endif`,Fd=`#ifdef USE_ENVMAP
	#if defined( USE_BUMPMAP ) || defined( USE_NORMALMAP ) || defined( PHONG ) || defined( LAMBERT )
		#define ENV_WORLDPOS
	#endif
	#ifdef ENV_WORLDPOS
		
		varying vec3 vWorldPosition;
	#else
		varying vec3 vReflect;
		uniform float refractionRatio;
	#endif
#endif`,Od=`#ifdef USE_ENVMAP
	#ifdef ENV_WORLDPOS
		vWorldPosition = worldPosition.xyz;
	#else
		vec3 cameraToVertex;
		if ( isOrthographic ) {
			cameraToVertex = normalize( vec3( - viewMatrix[ 0 ][ 2 ], - viewMatrix[ 1 ][ 2 ], - viewMatrix[ 2 ][ 2 ] ) );
		} else {
			cameraToVertex = normalize( worldPosition.xyz - cameraPosition );
		}
		vec3 worldNormal = transformNormalByInverseViewMatrix( transformedNormal, viewMatrix );
		#ifdef ENVMAP_MODE_REFLECTION
			vReflect = reflect( cameraToVertex, worldNormal );
		#else
			vReflect = refract( cameraToVertex, worldNormal, refractionRatio );
		#endif
	#endif
#endif`,Bd=`#ifdef USE_FOG
	vFogDepth = - mvPosition.z;
#endif`,kd=`#ifdef USE_FOG
	varying float vFogDepth;
#endif`,zd=`#ifdef USE_FOG
	#ifdef FOG_EXP2
		float fogFactor = 1.0 - exp( - fogDensity * fogDensity * vFogDepth * vFogDepth );
	#else
		float fogFactor = smoothstep( fogNear, fogFar, vFogDepth );
	#endif
	gl_FragColor.rgb = mix( gl_FragColor.rgb, fogColor, fogFactor );
#endif`,Gd=`#ifdef USE_FOG
	uniform vec3 fogColor;
	varying float vFogDepth;
	#ifdef FOG_EXP2
		uniform float fogDensity;
	#else
		uniform float fogNear;
		uniform float fogFar;
	#endif
#endif`,Hd=`#ifdef USE_GRADIENTMAP
	uniform sampler2D gradientMap;
#endif
vec3 getGradientIrradiance( vec3 normal, vec3 lightDirection ) {
	float dotNL = dot( normal, lightDirection );
	vec2 coord = vec2( dotNL * 0.5 + 0.5, 0.0 );
	#ifdef USE_GRADIENTMAP
		return vec3( texture2D( gradientMap, coord ).r );
	#else
		vec2 fw = fwidth( coord ) * 0.5;
		return mix( vec3( 0.7 ), vec3( 1.0 ), smoothstep( 0.7 - fw.x, 0.7 + fw.x, coord.x ) );
	#endif
}`,Vd=`#ifdef USE_LIGHTMAP
	uniform sampler2D lightMap;
	uniform float lightMapIntensity;
#endif`,Wd=`LambertMaterial material;
material.diffuseColor = diffuseColor.rgb;
material.specularStrength = specularStrength;`,Xd=`varying vec3 vViewPosition;
struct LambertMaterial {
	vec3 diffuseColor;
	float specularStrength;
};
void RE_Direct_Lambert( const in IncidentLight directLight, const in vec3 geometryPosition, const in vec3 geometryNormal, const in vec3 geometryViewDir, const in vec3 geometryClearcoatNormal, const in LambertMaterial material, inout ReflectedLight reflectedLight ) {
	float dotNL = saturate( dot( geometryNormal, directLight.direction ) );
	vec3 irradiance = dotNL * directLight.color;
	reflectedLight.directDiffuse += irradiance * BRDF_Lambert( material.diffuseColor );
}
void RE_IndirectDiffuse_Lambert( const in vec3 irradiance, const in vec3 geometryPosition, const in vec3 geometryNormal, const in vec3 geometryViewDir, const in vec3 geometryClearcoatNormal, const in LambertMaterial material, inout ReflectedLight reflectedLight ) {
	reflectedLight.indirectDiffuse += irradiance * BRDF_Lambert( material.diffuseColor );
}
#define RE_Direct				RE_Direct_Lambert
#define RE_IndirectDiffuse		RE_IndirectDiffuse_Lambert`,qd=`uniform bool receiveShadow;
uniform vec3 ambientLightColor;
#if defined( USE_LIGHT_PROBES )
	uniform vec3 lightProbe[ 9 ];
#endif
vec3 shGetIrradianceAt( in vec3 normal, in vec3 shCoefficients[ 9 ] ) {
	float x = normal.x, y = normal.y, z = normal.z;
	vec3 result = shCoefficients[ 0 ] * 0.886227;
	result += shCoefficients[ 1 ] * 2.0 * 0.511664 * y;
	result += shCoefficients[ 2 ] * 2.0 * 0.511664 * z;
	result += shCoefficients[ 3 ] * 2.0 * 0.511664 * x;
	result += shCoefficients[ 4 ] * 2.0 * 0.429043 * x * y;
	result += shCoefficients[ 5 ] * 2.0 * 0.429043 * y * z;
	result += shCoefficients[ 6 ] * ( 0.743125 * z * z - 0.247708 );
	result += shCoefficients[ 7 ] * 2.0 * 0.429043 * x * z;
	result += shCoefficients[ 8 ] * 0.429043 * ( x * x - y * y );
	return result;
}
vec3 getLightProbeIrradiance( const in vec3 lightProbe[ 9 ], const in vec3 normal ) {
	vec3 worldNormal = transformNormalByInverseViewMatrix( normal, viewMatrix );
	vec3 irradiance = shGetIrradianceAt( worldNormal, lightProbe );
	return irradiance;
}
vec3 getAmbientLightIrradiance( const in vec3 ambientLightColor ) {
	vec3 irradiance = ambientLightColor;
	return irradiance;
}
float getDistanceAttenuation( const in float lightDistance, const in float cutoffDistance, const in float decayExponent ) {
	float distanceFalloff = 1.0 / max( pow( lightDistance, decayExponent ), 0.01 );
	if ( cutoffDistance > 0.0 ) {
		distanceFalloff *= pow2( saturate( 1.0 - pow4( lightDistance / cutoffDistance ) ) );
	}
	return distanceFalloff;
}
float getSpotAttenuation( const in float coneCosine, const in float penumbraCosine, const in float angleCosine ) {
	return smoothstep( coneCosine, penumbraCosine, angleCosine );
}
#if NUM_SUN_LIGHTS > 0
	struct SunLight {
		vec3 direction;
		vec3 color;
	};
	uniform SunLight sunLights[ NUM_SUN_LIGHTS ];
	void getSunLightInfo( const in SunLight sunLight, out IncidentLight light ) {
		light.color = sunLight.color;
		light.direction = sunLight.direction;
		light.visible = true;
	}
#endif
#if NUM_DIR_LIGHTS > 0
	struct DirectionalLight {
		vec3 direction;
		vec3 color;
	};
	uniform DirectionalLight directionalLights[ NUM_DIR_LIGHTS ];
	void getDirectionalLightInfo( const in DirectionalLight directionalLight, out IncidentLight light ) {
		light.color = directionalLight.color;
		light.direction = directionalLight.direction;
		light.visible = true;
	}
#endif
#if NUM_POINT_LIGHTS > 0
	struct PointLight {
		vec3 position;
		vec3 color;
		float distance;
		float decay;
	};
	uniform PointLight pointLights[ NUM_POINT_LIGHTS ];
	void getPointLightInfo( const in PointLight pointLight, const in vec3 geometryPosition, out IncidentLight light ) {
		vec3 lVector = pointLight.position - geometryPosition;
		light.direction = normalize( lVector );
		float lightDistance = length( lVector );
		light.color = pointLight.color;
		light.color *= getDistanceAttenuation( lightDistance, pointLight.distance, pointLight.decay );
		light.visible = ( light.color != vec3( 0.0 ) );
	}
#endif
#if NUM_SPOT_LIGHTS > 0
	struct SpotLight {
		vec3 position;
		vec3 direction;
		vec3 color;
		float distance;
		float decay;
		float coneCos;
		float penumbraCos;
	};
	uniform SpotLight spotLights[ NUM_SPOT_LIGHTS ];
	void getSpotLightInfo( const in SpotLight spotLight, const in vec3 geometryPosition, out IncidentLight light ) {
		vec3 lVector = spotLight.position - geometryPosition;
		light.direction = normalize( lVector );
		float angleCos = dot( light.direction, spotLight.direction );
		float spotAttenuation = getSpotAttenuation( spotLight.coneCos, spotLight.penumbraCos, angleCos );
		if ( spotAttenuation > 0.0 ) {
			float lightDistance = length( lVector );
			light.color = spotLight.color * spotAttenuation;
			light.color *= getDistanceAttenuation( lightDistance, spotLight.distance, spotLight.decay );
			light.visible = ( light.color != vec3( 0.0 ) );
		} else {
			light.color = vec3( 0.0 );
			light.visible = false;
		}
	}
#endif
#if NUM_RECT_AREA_LIGHTS > 0
	struct RectAreaLight {
		vec3 color;
		vec3 position;
		vec3 halfWidth;
		vec3 halfHeight;
	};
	uniform sampler2D ltc_1;	uniform sampler2D ltc_2;
	uniform RectAreaLight rectAreaLights[ NUM_RECT_AREA_LIGHTS ];
#endif
#if NUM_HEMI_LIGHTS > 0
	struct HemisphereLight {
		vec3 direction;
		vec3 skyColor;
		vec3 groundColor;
	};
	uniform HemisphereLight hemisphereLights[ NUM_HEMI_LIGHTS ];
	vec3 getHemisphereLightIrradiance( const in HemisphereLight hemiLight, const in vec3 normal ) {
		float dotNL = dot( normal, hemiLight.direction );
		float hemiDiffuseWeight = 0.5 * dotNL + 0.5;
		vec3 irradiance = mix( hemiLight.groundColor, hemiLight.skyColor, hemiDiffuseWeight );
		return irradiance;
	}
#endif
#include <lightprobes_pars_fragment>`,Yd=`#ifdef USE_ENVMAP
	vec3 getIBLIrradiance( const in vec3 normal ) {
		#ifdef ENVMAP_TYPE_PMREM
			vec3 worldNormal = transformNormalByInverseViewMatrix( normal, viewMatrix );
			vec4 envMapColor = textureLod( envMap, envMapRotation * worldNormal, ENVMAP_MAX_LOD );
			return PI * envMapColor.rgb * envMapIntensity;
		#else
			return vec3( 0.0 );
		#endif
	}
	vec3 getIBLRadiance( const in vec3 viewDir, const in vec3 normal, const in float roughness ) {
		#ifdef ENVMAP_TYPE_PMREM
			vec3 reflectVec = reflect( - viewDir, normal );
			reflectVec = normalize( mix( reflectVec, normal, pow4( roughness ) ) );
			reflectVec = transformDirectionByInverseViewMatrix( reflectVec, viewMatrix );
			vec4 envMapColor = textureLod( envMap, envMapRotation * reflectVec, roughnessToMip( roughness ) );
			return envMapColor.rgb * envMapIntensity;
		#else
			return vec3( 0.0 );
		#endif
	}
	#ifdef USE_RETROREFLECTION
		vec3 getIBLRetroRadiance( const in vec3 viewDir, const in vec3 normal, const in float roughness ) {
			#ifdef ENVMAP_TYPE_PMREM
				vec3 retroVec = normalize( mix( viewDir, normal, pow4( roughness ) ) );
				retroVec = transformDirectionByInverseViewMatrix( retroVec, viewMatrix );
				vec4 envMapColor = textureLod( envMap, envMapRotation * retroVec, roughnessToMip( roughness ) );
				return envMapColor.rgb * envMapIntensity;
			#else
				return vec3( 0.0 );
			#endif
		}
	#endif
	#ifdef USE_ANISOTROPY
		vec3 getIBLAnisotropyRadiance( const in vec3 viewDir, const in vec3 normal, const in float roughness, const in vec3 bitangent, const in float anisotropy ) {
			#ifdef ENVMAP_TYPE_PMREM
				vec3 bentNormal = cross( bitangent, viewDir );
				bentNormal = normalize( cross( bentNormal, bitangent ) );
				bentNormal = normalize( mix( bentNormal, normal, pow2( pow2( 1.0 - anisotropy * ( 1.0 - roughness ) ) ) ) );
				return getIBLRadiance( viewDir, bentNormal, roughness );
			#else
				return vec3( 0.0 );
			#endif
		}
		#ifdef USE_RETROREFLECTION
			vec3 getIBLAnisotropyRetroRadiance( const in vec3 viewDir, const in vec3 normal, const in float roughness, const in vec3 bitangent, const in float anisotropy ) {
				#ifdef ENVMAP_TYPE_PMREM
					vec3 bentNormal = cross( bitangent, viewDir );
					bentNormal = normalize( cross( bentNormal, bitangent ) );
					bentNormal = normalize( mix( bentNormal, normal, pow2( pow2( 1.0 - anisotropy * ( 1.0 - roughness ) ) ) ) );
					return getIBLRetroRadiance( viewDir, bentNormal, roughness );
				#else
					return vec3( 0.0 );
				#endif
			}
		#endif
	#endif
#endif`,Kd=`ToonMaterial material;
material.diffuseColor = diffuseColor.rgb;`,Zd=`varying vec3 vViewPosition;
struct ToonMaterial {
	vec3 diffuseColor;
};
void RE_Direct_Toon( const in IncidentLight directLight, const in vec3 geometryPosition, const in vec3 geometryNormal, const in vec3 geometryViewDir, const in vec3 geometryClearcoatNormal, const in ToonMaterial material, inout ReflectedLight reflectedLight ) {
	vec3 irradiance = getGradientIrradiance( geometryNormal, directLight.direction ) * directLight.color;
	reflectedLight.directDiffuse += irradiance * BRDF_Lambert( material.diffuseColor );
}
void RE_IndirectDiffuse_Toon( const in vec3 irradiance, const in vec3 geometryPosition, const in vec3 geometryNormal, const in vec3 geometryViewDir, const in vec3 geometryClearcoatNormal, const in ToonMaterial material, inout ReflectedLight reflectedLight ) {
	reflectedLight.indirectDiffuse += irradiance * BRDF_Lambert( material.diffuseColor );
}
#define RE_Direct				RE_Direct_Toon
#define RE_IndirectDiffuse		RE_IndirectDiffuse_Toon`,$d=`BlinnPhongMaterial material;
material.diffuseColor = diffuseColor.rgb;
material.specularColor = specular;
material.specularShininess = shininess;
material.specularStrength = specularStrength;`,Jd=`varying vec3 vViewPosition;
struct BlinnPhongMaterial {
	vec3 diffuseColor;
	vec3 specularColor;
	float specularShininess;
	float specularStrength;
};
void RE_Direct_BlinnPhong( const in IncidentLight directLight, const in vec3 geometryPosition, const in vec3 geometryNormal, const in vec3 geometryViewDir, const in vec3 geometryClearcoatNormal, const in BlinnPhongMaterial material, inout ReflectedLight reflectedLight ) {
	float dotNL = saturate( dot( geometryNormal, directLight.direction ) );
	vec3 irradiance = dotNL * directLight.color;
	reflectedLight.directDiffuse += irradiance * BRDF_Lambert( material.diffuseColor );
	reflectedLight.directSpecular += irradiance * BRDF_BlinnPhong( directLight.direction, geometryViewDir, geometryNormal, material.specularColor, material.specularShininess ) * material.specularStrength;
}
void RE_IndirectDiffuse_BlinnPhong( const in vec3 irradiance, const in vec3 geometryPosition, const in vec3 geometryNormal, const in vec3 geometryViewDir, const in vec3 geometryClearcoatNormal, const in BlinnPhongMaterial material, inout ReflectedLight reflectedLight ) {
	reflectedLight.indirectDiffuse += irradiance * BRDF_Lambert( material.diffuseColor );
}
#define RE_Direct				RE_Direct_BlinnPhong
#define RE_IndirectDiffuse		RE_IndirectDiffuse_BlinnPhong`,Qd=`PhysicalMaterial material;
material.diffuseColor = diffuseColor.rgb;
material.diffuseContribution = diffuseColor.rgb * ( 1.0 - metalnessFactor );
material.metalness = metalnessFactor;
vec3 dxy = max( abs( dFdx( nonPerturbedNormal ) ), abs( dFdy( nonPerturbedNormal ) ) );
float geometryRoughness = max( max( dxy.x, dxy.y ), dxy.z );
material.roughness = max( roughnessFactor, 0.045 );
material.roughness += geometryRoughness;
material.roughness = min( material.roughness, 1.0 );
#ifdef USE_DIFFUSE_ROUGHNESS
	float diffuseRoughnessFactor = diffuseRoughness;
	#ifdef USE_DIFFUSE_ROUGHNESSMAP
		diffuseRoughnessFactor *= texture2D( diffuseRoughnessMap, vDiffuseRoughnessMapUv ).r;
	#endif
	material.diffuseRoughness = saturate( diffuseRoughnessFactor );
#endif
#ifdef IOR
	material.ior = ior;
	#ifdef USE_SPECULAR
		float specularIntensityFactor = specularIntensity;
		vec3 specularColorFactor = specularColor;
		#ifdef USE_SPECULAR_COLORMAP
			specularColorFactor *= texture2D( specularColorMap, vSpecularColorMapUv ).rgb;
		#endif
		#ifdef USE_SPECULAR_INTENSITYMAP
			specularIntensityFactor *= texture2D( specularIntensityMap, vSpecularIntensityMapUv ).a;
		#endif
		material.specularF90 = mix( specularIntensityFactor, 1.0, metalnessFactor );
	#else
		float specularIntensityFactor = 1.0;
		vec3 specularColorFactor = vec3( 1.0 );
		material.specularF90 = 1.0;
	#endif
	material.specularColor = min( pow2( ( material.ior - 1.0 ) / ( material.ior + 1.0 ) ) * specularColorFactor, vec3( 1.0 ) ) * specularIntensityFactor;
	material.specularColorBlended = mix( material.specularColor, diffuseColor.rgb, metalnessFactor );
#else
	material.specularColor = vec3( 0.04 );
	material.specularColorBlended = mix( material.specularColor, diffuseColor.rgb, metalnessFactor );
	material.specularF90 = 1.0;
#endif
#ifdef USE_CLEARCOAT
	material.clearcoat = clearcoat;
	material.clearcoatRoughness = clearcoatRoughness;
	material.clearcoatF0 = vec3( 0.04 );
	material.clearcoatF90 = 1.0;
	#ifdef USE_CLEARCOATMAP
		material.clearcoat *= texture2D( clearcoatMap, vClearcoatMapUv ).x;
	#endif
	#ifdef USE_CLEARCOAT_ROUGHNESSMAP
		material.clearcoatRoughness *= texture2D( clearcoatRoughnessMap, vClearcoatRoughnessMapUv ).y;
	#endif
	material.clearcoat = saturate( material.clearcoat );	material.clearcoatRoughness = max( material.clearcoatRoughness, 0.045 );
	material.clearcoatRoughness += geometryRoughness;
	material.clearcoatRoughness = min( material.clearcoatRoughness, 1.0 );
#endif
#ifdef USE_DISPERSION
	material.dispersion = dispersion;
#endif
#ifdef USE_RETROREFLECTION
	material.retroreflectivity = retroreflectivity;
#endif
#ifdef USE_IRIDESCENCE
	material.iridescence = iridescence;
	material.iridescenceIOR = iridescenceIOR;
	#ifdef USE_IRIDESCENCEMAP
		material.iridescence *= texture2D( iridescenceMap, vIridescenceMapUv ).r;
	#endif
	#ifdef USE_IRIDESCENCE_THICKNESSMAP
		material.iridescenceThickness = (iridescenceThicknessMaximum - iridescenceThicknessMinimum) * texture2D( iridescenceThicknessMap, vIridescenceThicknessMapUv ).g + iridescenceThicknessMinimum;
	#else
		material.iridescenceThickness = iridescenceThicknessMaximum;
	#endif
#endif
#ifdef USE_SHEEN
	material.sheenColor = sheenColor;
	#ifdef USE_SHEEN_COLORMAP
		material.sheenColor *= texture2D( sheenColorMap, vSheenColorMapUv ).rgb;
	#endif
	material.sheenRoughness = clamp( sheenRoughness, 0.0001, 1.0 );
	#ifdef USE_SHEEN_ROUGHNESSMAP
		material.sheenRoughness *= texture2D( sheenRoughnessMap, vSheenRoughnessMapUv ).a;
	#endif
#endif
#ifdef USE_ANISOTROPY
	#ifdef USE_ANISOTROPYMAP
		mat2 anisotropyMat = mat2( anisotropyVector.x, anisotropyVector.y, - anisotropyVector.y, anisotropyVector.x );
		vec3 anisotropyPolar = texture2D( anisotropyMap, vAnisotropyMapUv ).rgb;
		vec2 anisotropyV = anisotropyMat * normalize( 2.0 * anisotropyPolar.rg - vec2( 1.0 ) ) * anisotropyPolar.b;
	#else
		vec2 anisotropyV = anisotropyVector;
	#endif
	material.anisotropy = length( anisotropyV );
	if( material.anisotropy == 0.0 ) {
		anisotropyV = vec2( 1.0, 0.0 );
	} else {
		anisotropyV /= material.anisotropy;
		material.anisotropy = saturate( material.anisotropy );
	}
	material.alphaT = mix( pow2( material.roughness ), 1.0, pow2( material.anisotropy ) );
	material.anisotropyT = tbn[ 0 ] * anisotropyV.x + tbn[ 1 ] * anisotropyV.y;
	material.anisotropyB = tbn[ 1 ] * anisotropyV.x - tbn[ 0 ] * anisotropyV.y;
#endif`,jd=`uniform sampler2D dfgLUT;
struct PhysicalMaterial {
	vec3 diffuseColor;
	vec3 diffuseContribution;
	vec3 specularColor;
	vec3 specularColorBlended;
	float roughness;
	float metalness;
	float specularF90;
	float dispersion;
	vec2 dfg;
	vec3 multiScatteringCompensation;
	#ifdef USE_DIFFUSE_ROUGHNESS
		float diffuseRoughness;
	#endif
	#ifdef USE_RETROREFLECTION
		float retroreflectivity;
	#endif
	#ifdef USE_CLEARCOAT
		float clearcoat;
		float clearcoatRoughness;
		vec3 clearcoatF0;
		float clearcoatF90;
	#endif
	#ifdef USE_IRIDESCENCE
		float iridescence;
		float iridescenceIOR;
		float iridescenceThickness;
		vec3 iridescenceFresnel;
		vec3 iridescenceF0Dielectric;
		vec3 iridescenceF0Metallic;
	#endif
	#ifdef USE_SHEEN
		vec3 sheenColor;
		float sheenRoughness;
	#endif
	#ifdef IOR
		float ior;
	#endif
	#ifdef USE_TRANSMISSION
		float transmission;
		float transmissionAlpha;
		float thickness;
		float attenuationDistance;
		vec3 attenuationColor;
	#endif
	#ifdef USE_ANISOTROPY
		float anisotropy;
		float alphaT;
		vec3 anisotropyT;
		vec3 anisotropyB;
	#endif
};
vec3 clearcoatSpecularDirect = vec3( 0.0 );
vec3 clearcoatSpecularIndirect = vec3( 0.0 );
vec3 sheenSpecularDirect = vec3( 0.0 );
vec3 sheenSpecularIndirect = vec3(0.0 );
vec3 Schlick_to_F0( const in vec3 f, const in float f90, const in float dotVH ) {
    float x = clamp( 1.0 - dotVH, 0.0, 1.0 );
    float x2 = x * x;
    float x5 = clamp( x * x2 * x2, 0.0, 0.9999 );
    return ( f - vec3( f90 ) * x5 ) / ( 1.0 - x5 );
}
#ifdef USE_DIFFUSE_ROUGHNESS
	const float EON_EPSILON = 1e-7;
	float FON_DirectionalAlbedo( const in float mu, const in float roughness, const in float A ) {
		float muComp = 1.0 - mu;
		float gOverPi = muComp * ( 0.0571085289 + muComp * ( 0.491881867 + muComp * ( - 0.332181442 + muComp * 0.0714429953 ) ) );
		return A * ( 1.0 + roughness * gOverPi );
	}
	vec3 BRDF_EON( const in vec3 lightDir, const in vec3 viewDir, const in vec3 normal, const in vec3 diffuseColor, const in float roughness ) {
		vec3 rho = saturate( diffuseColor );
		if ( roughness <= EON_EPSILON ) return BRDF_Lambert( rho );
		float dotNL = saturate( dot( normal, lightDir ) );
		float dotNV = saturate( dot( normal, viewDir ) );
		float s = dot( lightDir, viewDir ) - dotNL * dotNV;
		float sOverT = ( s > 0.0 ) ? s / max( max( dotNL, dotNV ), EON_EPSILON ) : s;
		float A = 1.0 / ( 1.0 + ( 0.5 - 2.0 / ( 3.0 * PI ) ) * roughness );
		vec3 singleScatter = rho * RECIPROCAL_PI * A * ( 1.0 + roughness * sOverT );
		float averageAlbedo = A * ( 1.0 + ( 2.0 / 3.0 - 28.0 / ( 15.0 * PI ) ) * roughness );
		float albedoV = FON_DirectionalAlbedo( dotNV, roughness, A );
		float albedoL = FON_DirectionalAlbedo( dotNL, roughness, A );
		vec3 rhoMultiScatter = rho * rho * averageAlbedo / max( vec3( EON_EPSILON ), vec3( 1.0 ) - rho * ( 1.0 - averageAlbedo ) );
		vec3 multiScatter = rhoMultiScatter * RECIPROCAL_PI
			* max( EON_EPSILON, 1.0 - albedoV )
			* max( EON_EPSILON, 1.0 - albedoL )
			/ max( EON_EPSILON, 1.0 - averageAlbedo );
		return singleScatter + multiScatter;
	}
	vec3 EON_DirectionalAlbedo( const in vec3 diffuseColor, const in float roughness, const in float dotNV ) {
		vec3 rho = saturate( diffuseColor );
		if ( roughness <= EON_EPSILON ) return rho;
		float A = 1.0 / ( 1.0 + ( 0.5 - 2.0 / ( 3.0 * PI ) ) * roughness );
		float directionalAlbedo = FON_DirectionalAlbedo( dotNV, roughness, A );
		float averageAlbedo = A * ( 1.0 + ( 2.0 / 3.0 - 28.0 / ( 15.0 * PI ) ) * roughness );
		vec3 rhoMultiScatter = rho * rho * averageAlbedo / max( vec3( EON_EPSILON ), vec3( 1.0 ) - rho * ( 1.0 - averageAlbedo ) );
		return rho * directionalAlbedo + rhoMultiScatter * ( 1.0 - directionalAlbedo );
	}
#endif
float V_GGX_SmithCorrelated( const in float alpha, const in float dotNL, const in float dotNV ) {
	float a2 = pow2( alpha );
	float gv = dotNL * sqrt( a2 + ( 1.0 - a2 ) * pow2( dotNV ) );
	float gl = dotNV * sqrt( a2 + ( 1.0 - a2 ) * pow2( dotNL ) );
	return 0.5 / max( gv + gl, EPSILON );
}
float D_GGX( const in float alpha, const in float dotNH ) {
	float a2 = pow2( alpha );
	float denom = pow2( dotNH ) * ( a2 - 1.0 ) + 1.0;
	return RECIPROCAL_PI * a2 / pow2( denom );
}
#ifdef USE_ANISOTROPY
	float V_GGX_SmithCorrelated_Anisotropic( const in float alphaT, const in float alphaB, const in float dotTV, const in float dotBV, const in float dotTL, const in float dotBL, const in float dotNV, const in float dotNL ) {
		float gv = dotNL * length( vec3( alphaT * dotTV, alphaB * dotBV, dotNV ) );
		float gl = dotNV * length( vec3( alphaT * dotTL, alphaB * dotBL, dotNL ) );
		return 0.5 / max( gv + gl, EPSILON );
	}
	float D_GGX_Anisotropic( const in float alphaT, const in float alphaB, const in float dotNH, const in float dotTH, const in float dotBH ) {
		float a2 = alphaT * alphaB;
		highp vec3 v = vec3( alphaB * dotTH, alphaT * dotBH, a2 * dotNH );
		highp float v2 = dot( v, v );
		float w2 = a2 / v2;
		return RECIPROCAL_PI * a2 * pow2 ( w2 );
	}
#endif
#ifdef USE_CLEARCOAT
	vec3 BRDF_GGX_Clearcoat( const in vec3 lightDir, const in vec3 viewDir, const in vec3 normal, const in PhysicalMaterial material) {
		vec3 f0 = material.clearcoatF0;
		float f90 = material.clearcoatF90;
		float roughness = max( material.clearcoatRoughness, 0.045 );
		float alpha = pow2( roughness );
		vec3 halfDir = normalize( lightDir + viewDir );
		float dotNL = saturate( dot( normal, lightDir ) );
		float dotNV = saturate( dot( normal, viewDir ) );
		float dotNH = saturate( dot( normal, halfDir ) );
		float dotVH = saturate( dot( viewDir, halfDir ) );
		vec3 F = F_Schlick( f0, f90, dotVH );
		float V = V_GGX_SmithCorrelated( alpha, dotNL, dotNV );
		float D = D_GGX( alpha, dotNH );
		return F * ( V * D );
	}
#endif
vec3 BRDF_GGX( const in vec3 lightDir, const in vec3 viewDir, const in vec3 normal, const in PhysicalMaterial material ) {
	vec3 f0 = material.specularColorBlended;
	float f90 = material.specularF90;
	float roughness = max( material.roughness, 0.045 );
	float alpha = pow2( roughness );
	vec3 halfDir = normalize( lightDir + viewDir );
	float dotNL = saturate( dot( normal, lightDir ) );
	float dotNV = saturate( dot( normal, viewDir ) );
	float dotNH = saturate( dot( normal, halfDir ) );
	float dotVH = saturate( dot( viewDir, halfDir ) );
	vec3 F = F_Schlick( f0, f90, dotVH );
	#ifdef USE_IRIDESCENCE
		F = mix( F, material.iridescenceFresnel, material.iridescence );
	#endif
	#ifdef USE_ANISOTROPY
		float dotTL = dot( material.anisotropyT, lightDir );
		float dotTV = dot( material.anisotropyT, viewDir );
		float dotTH = dot( material.anisotropyT, halfDir );
		float dotBL = dot( material.anisotropyB, lightDir );
		float dotBV = dot( material.anisotropyB, viewDir );
		float dotBH = dot( material.anisotropyB, halfDir );
		float alphaT = max( material.alphaT, alpha );
		float V = V_GGX_SmithCorrelated_Anisotropic( alphaT, alpha, dotTV, dotBV, dotTL, dotBL, dotNV, dotNL );
		float D = D_GGX_Anisotropic( alphaT, alpha, dotNH, dotTH, dotBH );
	#else
		float V = V_GGX_SmithCorrelated( alpha, dotNL, dotNV );
		float D = D_GGX( alpha, dotNH );
	#endif
	return F * ( V * D );
}
vec2 LTC_Uv( const in vec3 N, const in vec3 V, const in float roughness ) {
	const float LUT_SIZE = 64.0;
	const float LUT_SCALE = ( LUT_SIZE - 1.0 ) / LUT_SIZE;
	const float LUT_BIAS = 0.5 / LUT_SIZE;
	float dotNV = saturate( dot( N, V ) );
	vec2 uv = vec2( roughness, sqrt( 1.0 - dotNV ) );
	uv = uv * LUT_SCALE + LUT_BIAS;
	return uv;
}
float LTC_ClippedSphereFormFactor( const in vec3 f ) {
	float l = length( f );
	return max( ( l * l + f.z ) / ( l + 1.0 ), 0.0 );
}
vec3 LTC_EdgeVectorFormFactor( const in vec3 v1, const in vec3 v2 ) {
	float x = dot( v1, v2 );
	float y = abs( x );
	float a = 0.8543985 + ( 0.4965155 + 0.0145206 * y ) * y;
	float b = 3.4175940 + ( 4.1616724 + y ) * y;
	float v = a / b;
	float theta_sintheta = ( x > 0.0 ) ? v : 0.5 * inversesqrt( max( 1.0 - x * x, 1e-7 ) ) - v;
	return cross( v1, v2 ) * theta_sintheta;
}
vec3 LTC_Evaluate( const in vec3 N, const in vec3 V, const in vec3 P, const in mat3 mInv, const in vec3 rectCoords[ 4 ] ) {
	vec3 v1 = rectCoords[ 1 ] - rectCoords[ 0 ];
	vec3 v2 = rectCoords[ 3 ] - rectCoords[ 0 ];
	vec3 lightNormal = cross( v1, v2 );
	if( dot( lightNormal, P - rectCoords[ 0 ] ) < 0.0 ) return vec3( 0.0 );
	vec3 T1, T2;
	T1 = normalize( V - N * dot( V, N ) );
	T2 = - cross( N, T1 );
	mat3 mat = mInv * transpose( mat3( T1, T2, N ) );
	vec3 coords[ 4 ];
	coords[ 0 ] = mat * ( rectCoords[ 0 ] - P );
	coords[ 1 ] = mat * ( rectCoords[ 1 ] - P );
	coords[ 2 ] = mat * ( rectCoords[ 2 ] - P );
	coords[ 3 ] = mat * ( rectCoords[ 3 ] - P );
	coords[ 0 ] = normalize( coords[ 0 ] );
	coords[ 1 ] = normalize( coords[ 1 ] );
	coords[ 2 ] = normalize( coords[ 2 ] );
	coords[ 3 ] = normalize( coords[ 3 ] );
	vec3 vectorFormFactor = vec3( 0.0 );
	vectorFormFactor += LTC_EdgeVectorFormFactor( coords[ 0 ], coords[ 1 ] );
	vectorFormFactor += LTC_EdgeVectorFormFactor( coords[ 1 ], coords[ 2 ] );
	vectorFormFactor += LTC_EdgeVectorFormFactor( coords[ 2 ], coords[ 3 ] );
	vectorFormFactor += LTC_EdgeVectorFormFactor( coords[ 3 ], coords[ 0 ] );
	float result = LTC_ClippedSphereFormFactor( vectorFormFactor );
	return vec3( result );
}
#if defined( USE_SHEEN )
float D_Charlie( float roughness, float dotNH ) {
	float alpha = pow2( roughness );
	float invAlpha = 1.0 / alpha;
	float cos2h = dotNH * dotNH;
	float sin2h = max( 1.0 - cos2h, 0.0078125 );
	return ( 2.0 + invAlpha ) * pow( sin2h, invAlpha * 0.5 ) / ( 2.0 * PI );
}
float V_Neubelt( float dotNV, float dotNL ) {
	return saturate( 1.0 / ( 4.0 * ( dotNL + dotNV - dotNL * dotNV ) ) );
}
vec3 BRDF_Sheen( const in vec3 lightDir, const in vec3 viewDir, const in vec3 normal, vec3 sheenColor, const in float sheenRoughness ) {
	vec3 halfDir = normalize( lightDir + viewDir );
	float dotNL = saturate( dot( normal, lightDir ) );
	float dotNV = saturate( dot( normal, viewDir ) );
	float dotNH = saturate( dot( normal, halfDir ) );
	float D = D_Charlie( sheenRoughness, dotNH );
	float V = V_Neubelt( dotNV, dotNL );
	return sheenColor * ( D * V );
}
#endif
float IBLSheenBRDF( const in vec3 normal, const in vec3 viewDir, const in float roughness ) {
	float dotNV = saturate( dot( normal, viewDir ) );
	float r2 = roughness * roughness;
	float rInv = 1.0 / ( roughness + 0.1 );
	float a = -1.9362 + 1.0678 * roughness + 0.4573 * r2 - 0.8469 * rInv;
	float b = -0.6014 + 0.5538 * roughness - 0.4670 * r2 - 0.1255 * rInv;
	float DG = exp( a * dotNV + b );
	return saturate( DG );
}
vec3 EnvironmentBRDF( const in vec3 normal, const in vec3 viewDir, const in vec3 specularColor, const in float specularF90, const in float roughness ) {
	float dotNV = saturate( dot( normal, viewDir ) );
	vec2 fab = texture2D( dfgLUT, vec2( roughness, dotNV ) ).rg;
	return specularColor * fab.x + specularF90 * fab.y;
}
#ifdef USE_IRIDESCENCE
void computeMultiscatteringIridescence( const in vec2 fab, const in vec3 specularColor, const in float specularF90, const in float iridescence, const in vec3 iridescenceF0, inout vec3 singleScatter, inout vec3 multiScatter ) {
#else
void computeMultiscattering( const in vec2 fab, const in vec3 specularColor, const in float specularF90, inout vec3 singleScatter, inout vec3 multiScatter ) {
#endif
	#ifdef USE_IRIDESCENCE
		vec3 Fr = mix( specularColor, iridescenceF0, iridescence );
	#else
		vec3 Fr = specularColor;
	#endif
	vec3 FssEss = Fr * fab.x + specularF90 * fab.y;
	float Ess = fab.x + fab.y;
	float Ems = 1.0 - Ess;
	vec3 Favg = Fr + ( 1.0 - Fr ) * 0.047619;	vec3 Fms = FssEss * Favg / ( 1.0 - Ems * Favg );
	singleScatter += FssEss;
	multiScatter += Fms * Ems;
}
#if NUM_RECT_AREA_LIGHTS > 0
	void RE_Direct_RectArea_Physical( const in RectAreaLight rectAreaLight, const in vec3 geometryPosition, const in vec3 geometryNormal, const in vec3 geometryViewDir, const in vec3 geometryClearcoatNormal, const in PhysicalMaterial material, inout ReflectedLight reflectedLight ) {
		vec3 normal = geometryNormal;
		vec3 viewDir = geometryViewDir;
		vec3 position = geometryPosition;
		vec3 lightPos = rectAreaLight.position;
		vec3 halfWidth = rectAreaLight.halfWidth;
		vec3 halfHeight = rectAreaLight.halfHeight;
		vec3 lightColor = rectAreaLight.color;
		float roughness = material.roughness;
		vec3 rectCoords[ 4 ];
		rectCoords[ 0 ] = lightPos + halfWidth - halfHeight;		rectCoords[ 1 ] = lightPos - halfWidth - halfHeight;
		rectCoords[ 2 ] = lightPos - halfWidth + halfHeight;
		rectCoords[ 3 ] = lightPos + halfWidth + halfHeight;
		vec2 uv = LTC_Uv( normal, viewDir, roughness );
		vec4 t1 = texture2D( ltc_1, uv );
		vec4 t2 = texture2D( ltc_2, uv );
		mat3 mInv = mat3(
			vec3( t1.x, 0, t1.y ),
			vec3(    0, 1,    0 ),
			vec3( t1.z, 0, t1.w )
		);
		vec3 fresnel = ( material.specularColorBlended * t2.x + ( material.specularF90 - material.specularColorBlended ) * t2.y );
		reflectedLight.directSpecular += lightColor * fresnel * LTC_Evaluate( normal, viewDir, position, mInv, rectCoords );
		reflectedLight.directDiffuse += lightColor * material.diffuseContribution * LTC_Evaluate( normal, viewDir, position, mat3( 1.0 ), rectCoords );
		#ifdef USE_CLEARCOAT
			vec3 Ncc = geometryClearcoatNormal;
			vec2 uvClearcoat = LTC_Uv( Ncc, viewDir, material.clearcoatRoughness );
			vec4 t1Clearcoat = texture2D( ltc_1, uvClearcoat );
			vec4 t2Clearcoat = texture2D( ltc_2, uvClearcoat );
			mat3 mInvClearcoat = mat3(
				vec3( t1Clearcoat.x, 0, t1Clearcoat.y ),
				vec3(             0, 1,             0 ),
				vec3( t1Clearcoat.z, 0, t1Clearcoat.w )
			);
			vec3 fresnelClearcoat = material.clearcoatF0 * t2Clearcoat.x + ( material.clearcoatF90 - material.clearcoatF0 ) * t2Clearcoat.y;
			clearcoatSpecularDirect += lightColor * fresnelClearcoat * LTC_Evaluate( Ncc, viewDir, position, mInvClearcoat, rectCoords );
		#endif
	}
#endif
void RE_Direct_Physical( const in IncidentLight directLight, const in vec3 geometryPosition, const in vec3 geometryNormal, const in vec3 geometryViewDir, const in vec3 geometryClearcoatNormal, const in PhysicalMaterial material, inout ReflectedLight reflectedLight ) {
	float dotNL = saturate( dot( geometryNormal, directLight.direction ) );
	vec3 irradiance = dotNL * directLight.color;
	#ifdef USE_CLEARCOAT
		float dotNLcc = saturate( dot( geometryClearcoatNormal, directLight.direction ) );
		vec3 ccIrradiance = dotNLcc * directLight.color;
		clearcoatSpecularDirect += ccIrradiance * BRDF_GGX_Clearcoat( directLight.direction, geometryViewDir, geometryClearcoatNormal, material );
	#endif
	#ifdef USE_SHEEN
 
 		sheenSpecularDirect += irradiance * BRDF_Sheen( directLight.direction, geometryViewDir, geometryNormal, material.sheenColor, material.sheenRoughness );
 
 		float sheenAlbedoV = IBLSheenBRDF( geometryNormal, geometryViewDir, material.sheenRoughness );
 		float sheenAlbedoL = IBLSheenBRDF( geometryNormal, directLight.direction, material.sheenRoughness );
 
 		float sheenEnergyComp = 1.0 - max3( material.sheenColor ) * max( sheenAlbedoV, sheenAlbedoL );
 
 		irradiance *= sheenEnergyComp;
 
 	#endif
	vec3 specularBRDF = BRDF_GGX( directLight.direction, geometryViewDir, geometryNormal, material );
	#ifdef USE_RETROREFLECTION
		vec3 retroViewDir = reflect( - geometryViewDir, geometryNormal );
		vec3 retroSpecularBRDF = BRDF_GGX( directLight.direction, retroViewDir, geometryNormal, material );
		specularBRDF = mix( specularBRDF, retroSpecularBRDF, saturate( material.retroreflectivity ) );
	#endif
	reflectedLight.directSpecular += irradiance * specularBRDF * material.multiScatteringCompensation;
	vec3 halfDir = normalize( directLight.direction + geometryViewDir );
	float dotVH = saturate( dot( geometryViewDir, halfDir ) );
	vec3 F = F_Schlick( material.specularColor, material.specularF90, dotVH );
	#ifdef USE_RETROREFLECTION
		vec3 retroHalfDir = normalize( directLight.direction + retroViewDir );
		float dotRetroVH = saturate( dot( retroViewDir, retroHalfDir ) );
		vec3 retroF = F_Schlick( material.specularColor, material.specularF90, dotRetroVH );
		F = mix( F, retroF, saturate( material.retroreflectivity ) );
	#endif
	#ifdef USE_DIFFUSE_ROUGHNESS
		vec3 diffuseBRDF = BRDF_EON( directLight.direction, geometryViewDir, geometryNormal, material.diffuseColor, material.diffuseRoughness ) * ( 1.0 - material.metalness );
	#else
		vec3 diffuseBRDF = BRDF_Lambert( material.diffuseContribution );
	#endif
	reflectedLight.directDiffuse += irradiance * diffuseBRDF * ( 1.0 - F );
}
void RE_IndirectDiffuse_Physical( const in vec3 irradiance, const in vec3 geometryPosition, const in vec3 geometryNormal, const in vec3 geometryViewDir, const in vec3 geometryClearcoatNormal, const in PhysicalMaterial material, inout ReflectedLight reflectedLight ) {
	vec3 singleScattering = vec3( 0.0 );
	vec3 multiScattering = vec3( 0.0 );
	#ifdef USE_IRIDESCENCE
		computeMultiscatteringIridescence( material.dfg, material.specularColor, material.specularF90, material.iridescence, material.iridescenceF0Dielectric, singleScattering, multiScattering );
	#else
		computeMultiscattering( material.dfg, material.specularColor, material.specularF90, singleScattering, multiScattering );
	#endif
	#ifdef USE_DIFFUSE_ROUGHNESS
		float dotNV = saturate( dot( geometryNormal, geometryViewDir ) );
		vec3 diffuseAlbedo = EON_DirectionalAlbedo( material.diffuseColor, material.diffuseRoughness, dotNV ) * ( 1.0 - material.metalness );
		vec3 diffuse = irradiance * RECIPROCAL_PI * diffuseAlbedo * ( 1.0 - singleScattering - multiScattering );
	#else
		vec3 diffuse = irradiance * BRDF_Lambert( material.diffuseContribution ) * ( 1.0 - singleScattering - multiScattering );
	#endif
	#ifdef USE_SHEEN
		float sheenAlbedo = IBLSheenBRDF( geometryNormal, geometryViewDir, material.sheenRoughness );
		sheenSpecularIndirect += irradiance * material.sheenColor * sheenAlbedo * RECIPROCAL_PI;
		float sheenEnergyComp = 1.0 - max3( material.sheenColor ) * sheenAlbedo;
		diffuse *= sheenEnergyComp;
	#endif
	reflectedLight.indirectDiffuse += diffuse;
}
void RE_IndirectSpecular_Physical( const in vec3 radiance, const in vec3 irradiance, const in vec3 clearcoatRadiance, const in vec3 geometryPosition, const in vec3 geometryNormal, const in vec3 geometryViewDir, const in vec3 geometryClearcoatNormal, const in PhysicalMaterial material, inout ReflectedLight reflectedLight) {
	#ifdef USE_CLEARCOAT
		clearcoatSpecularIndirect += clearcoatRadiance * EnvironmentBRDF( geometryClearcoatNormal, geometryViewDir, material.clearcoatF0, material.clearcoatF90, material.clearcoatRoughness );
	#endif
	#ifdef USE_SHEEN
		sheenSpecularIndirect += irradiance * material.sheenColor * IBLSheenBRDF( geometryNormal, geometryViewDir, material.sheenRoughness ) * RECIPROCAL_PI;
 	#endif
	vec3 singleScatteringDielectric = vec3( 0.0 );
	vec3 multiScatteringDielectric = vec3( 0.0 );
	vec3 singleScatteringMetallic = vec3( 0.0 );
	vec3 multiScatteringMetallic = vec3( 0.0 );
	#ifdef USE_IRIDESCENCE
		computeMultiscatteringIridescence( material.dfg, material.specularColor, material.specularF90, material.iridescence, material.iridescenceF0Dielectric, singleScatteringDielectric, multiScatteringDielectric );
		computeMultiscatteringIridescence( material.dfg, material.diffuseColor, material.specularF90, material.iridescence, material.iridescenceF0Metallic, singleScatteringMetallic, multiScatteringMetallic );
	#else
		computeMultiscattering( material.dfg, material.specularColor, material.specularF90, singleScatteringDielectric, multiScatteringDielectric );
		computeMultiscattering( material.dfg, material.diffuseColor, material.specularF90, singleScatteringMetallic, multiScatteringMetallic );
	#endif
	vec3 singleScattering = mix( singleScatteringDielectric, singleScatteringMetallic, material.metalness );
	vec3 multiScattering = mix( multiScatteringDielectric, multiScatteringMetallic, material.metalness );
	vec3 totalScatteringDielectric = singleScatteringDielectric + multiScatteringDielectric;
	#ifdef USE_DIFFUSE_ROUGHNESS
		float dotNV = saturate( dot( geometryNormal, geometryViewDir ) );
		vec3 diffuse = EON_DirectionalAlbedo( material.diffuseColor, material.diffuseRoughness, dotNV ) * ( 1.0 - material.metalness ) * ( 1.0 - totalScatteringDielectric );
	#else
		vec3 diffuse = material.diffuseContribution * ( 1.0 - totalScatteringDielectric );
	#endif
	vec3 cosineWeightedIrradiance = irradiance * RECIPROCAL_PI;
	vec3 indirectSpecular = radiance * singleScattering;
	indirectSpecular += multiScattering * cosineWeightedIrradiance;
	vec3 indirectDiffuse = diffuse * cosineWeightedIrradiance;
	#ifdef USE_SHEEN
		float sheenAlbedo = IBLSheenBRDF( geometryNormal, geometryViewDir, material.sheenRoughness );
		float sheenEnergyComp = 1.0 - max3( material.sheenColor ) * sheenAlbedo;
		indirectSpecular *= sheenEnergyComp;
		indirectDiffuse *= sheenEnergyComp;
	#endif
	reflectedLight.indirectSpecular += indirectSpecular;
	reflectedLight.indirectDiffuse += indirectDiffuse;
}
#define RE_Direct				RE_Direct_Physical
#define RE_Direct_RectArea		RE_Direct_RectArea_Physical
#define RE_IndirectDiffuse		RE_IndirectDiffuse_Physical
#define RE_IndirectSpecular		RE_IndirectSpecular_Physical
float computeSpecularOcclusion( const in float dotNV, const in float ambientOcclusion, const in float roughness ) {
	return saturate( pow( dotNV + ambientOcclusion, exp2( - 16.0 * roughness - 1.0 ) ) - 1.0 + ambientOcclusion );
}`,eu=`
vec3 geometryPosition = - vViewPosition;
vec3 geometryNormal = normal;
vec3 geometryViewDir = ( isOrthographic ) ? vec3( 0, 0, 1 ) : normalize( vViewPosition );
vec3 geometryClearcoatNormal = vec3( 0.0 );
#ifdef USE_CLEARCOAT
	geometryClearcoatNormal = clearcoatNormal;
#endif
#ifdef USE_IRIDESCENCE
	float dotNVi = saturate( dot( normal, geometryViewDir ) );
	if ( material.iridescenceThickness == 0.0 ) {
		material.iridescence = 0.0;
	} else {
		material.iridescence = saturate( material.iridescence );
	}
	if ( material.iridescence > 0.0 ) {
		vec3 iridescenceFresnelDielectric = evalIridescence( 1.0, material.iridescenceIOR, dotNVi, material.iridescenceThickness, material.specularColor );
		vec3 iridescenceFresnelMetallic = evalIridescence( 1.0, material.iridescenceIOR, dotNVi, material.iridescenceThickness, material.diffuseColor );
		material.iridescenceFresnel = mix( iridescenceFresnelDielectric, iridescenceFresnelMetallic, material.metalness );
		material.iridescenceF0Dielectric = Schlick_to_F0( iridescenceFresnelDielectric, 1.0, dotNVi );
		material.iridescenceF0Metallic = Schlick_to_F0( iridescenceFresnelMetallic, 1.0, dotNVi );
	}
#endif
#ifdef STANDARD
	float dotNVms = saturate( dot( geometryNormal, geometryViewDir ) );
	material.dfg = texture2D( dfgLUT, vec2( material.roughness, dotNVms ) ).rg;
	#if ( NUM_SUN_LIGHTS > 0 || NUM_DIR_LIGHTS > 0 || NUM_POINT_LIGHTS > 0 || NUM_SPOT_LIGHTS > 0 )
		float EssMs = material.dfg.x + material.dfg.y;
		material.multiScatteringCompensation = 1.0 + material.specularColorBlended * ( 1.0 / EssMs - 1.0 );
	#endif
#endif
IncidentLight directLight;
#if ( NUM_POINT_LIGHTS > 0 ) && defined( RE_Direct )
	PointLight pointLight;
	#if defined( USE_SHADOWMAP ) && NUM_POINT_LIGHT_SHADOWS > 0
	PointLightShadow pointLightShadow;
	#endif
	#pragma unroll_loop_start
	for ( int i = 0; i < NUM_POINT_LIGHTS; i ++ ) {
		pointLight = pointLights[ i ];
		getPointLightInfo( pointLight, geometryPosition, directLight );
		#if defined( USE_SHADOWMAP ) && ( UNROLLED_LOOP_INDEX < NUM_POINT_LIGHT_SHADOWS ) && ( defined( SHADOWMAP_TYPE_PCF ) || defined( SHADOWMAP_TYPE_BASIC ) )
		pointLightShadow = pointLightShadows[ i ];
		directLight.color *= ( directLight.visible && receiveShadow ) ? getPointShadow( pointShadowMap[ i ], pointLightShadow.shadowMapSize, pointLightShadow.shadowIntensity, pointLightShadow.shadowBias, pointLightShadow.shadowRadius, vPointShadowCoord[ i ], pointLightShadow.shadowCameraNear, pointLightShadow.shadowCameraFar ) : 1.0;
		#endif
		RE_Direct( directLight, geometryPosition, geometryNormal, geometryViewDir, geometryClearcoatNormal, material, reflectedLight );
	}
	#pragma unroll_loop_end
#endif
#if ( NUM_SPOT_LIGHTS > 0 ) && defined( RE_Direct )
	SpotLight spotLight;
	vec4 spotColor;
	vec3 spotLightCoord;
	bool inSpotLightMap;
	#if defined( USE_SHADOWMAP ) && NUM_SPOT_LIGHT_SHADOWS > 0
	SpotLightShadow spotLightShadow;
	#endif
	#pragma unroll_loop_start
	for ( int i = 0; i < NUM_SPOT_LIGHTS; i ++ ) {
		spotLight = spotLights[ i ];
		getSpotLightInfo( spotLight, geometryPosition, directLight );
		#if ( UNROLLED_LOOP_INDEX < NUM_SPOT_LIGHT_SHADOWS_WITH_MAPS )
		#define SPOT_LIGHT_MAP_INDEX UNROLLED_LOOP_INDEX
		#elif ( UNROLLED_LOOP_INDEX < NUM_SPOT_LIGHT_SHADOWS )
		#define SPOT_LIGHT_MAP_INDEX NUM_SPOT_LIGHT_MAPS
		#else
		#define SPOT_LIGHT_MAP_INDEX ( UNROLLED_LOOP_INDEX - NUM_SPOT_LIGHT_SHADOWS + NUM_SPOT_LIGHT_SHADOWS_WITH_MAPS )
		#endif
		#if ( SPOT_LIGHT_MAP_INDEX < NUM_SPOT_LIGHT_MAPS )
			spotLightCoord = vSpotLightCoord[ i ].xyz / vSpotLightCoord[ i ].w;
			inSpotLightMap = all( lessThan( abs( spotLightCoord * 2. - 1. ), vec3( 1.0 ) ) );
			spotColor = texture2D( spotLightMap[ SPOT_LIGHT_MAP_INDEX ], spotLightCoord.xy );
			directLight.color = inSpotLightMap ? directLight.color * spotColor.rgb : directLight.color;
		#endif
		#undef SPOT_LIGHT_MAP_INDEX
		#if defined( USE_SHADOWMAP ) && ( UNROLLED_LOOP_INDEX < NUM_SPOT_LIGHT_SHADOWS )
		spotLightShadow = spotLightShadows[ i ];
		directLight.color *= ( directLight.visible && receiveShadow ) ? getShadow( spotShadowMap[ i ], spotLightShadow.shadowMapSize, spotLightShadow.shadowIntensity, spotLightShadow.shadowBias, spotLightShadow.shadowRadius, vSpotLightCoord[ i ] ) : 1.0;
		#endif
		RE_Direct( directLight, geometryPosition, geometryNormal, geometryViewDir, geometryClearcoatNormal, material, reflectedLight );
	}
	#pragma unroll_loop_end
#endif
#if ( NUM_SUN_LIGHTS > 0 ) && defined( RE_Direct )
	SunLight sunLight;
	#if defined( USE_SHADOWMAP ) && NUM_SUN_LIGHT_SHADOWS > 0
	SunLightShadow sunLightShadow;
	#endif
	#pragma unroll_loop_start
	for ( int i = 0; i < NUM_SUN_LIGHTS; i ++ ) {
		sunLight = sunLights[ i ];
		getSunLightInfo( sunLight, directLight );
		#if defined( USE_SHADOWMAP ) && ( UNROLLED_LOOP_INDEX < NUM_SUN_LIGHT_SHADOWS )
		sunLightShadow = sunLightShadows[ i ];
		directLight.color *= ( directLight.visible && receiveShadow ) ? getSunShadow( sunShadowMap[ i ], sunLightShadow, UNROLLED_LOOP_INDEX ) : 1.0;
		#endif
		RE_Direct( directLight, geometryPosition, geometryNormal, geometryViewDir, geometryClearcoatNormal, material, reflectedLight );
	}
	#pragma unroll_loop_end
#endif
#if ( NUM_DIR_LIGHTS > 0 ) && defined( RE_Direct )
	DirectionalLight directionalLight;
	#if defined( USE_SHADOWMAP ) && NUM_DIR_LIGHT_SHADOWS > 0
	DirectionalLightShadow directionalLightShadow;
	#endif
	#pragma unroll_loop_start
	for ( int i = 0; i < NUM_DIR_LIGHTS; i ++ ) {
		directionalLight = directionalLights[ i ];
		getDirectionalLightInfo( directionalLight, directLight );
		#if defined( USE_SHADOWMAP ) && ( UNROLLED_LOOP_INDEX < NUM_DIR_LIGHT_SHADOWS )
		directionalLightShadow = directionalLightShadows[ i ];
		directLight.color *= ( directLight.visible && receiveShadow ) ? getShadow( directionalShadowMap[ i ], directionalLightShadow.shadowMapSize, directionalLightShadow.shadowIntensity, directionalLightShadow.shadowBias, directionalLightShadow.shadowRadius, vDirectionalShadowCoord[ i ] ) : 1.0;
		#endif
		RE_Direct( directLight, geometryPosition, geometryNormal, geometryViewDir, geometryClearcoatNormal, material, reflectedLight );
	}
	#pragma unroll_loop_end
#endif
#if ( NUM_RECT_AREA_LIGHTS > 0 ) && defined( RE_Direct_RectArea )
	RectAreaLight rectAreaLight;
	#pragma unroll_loop_start
	for ( int i = 0; i < NUM_RECT_AREA_LIGHTS; i ++ ) {
		rectAreaLight = rectAreaLights[ i ];
		RE_Direct_RectArea( rectAreaLight, geometryPosition, geometryNormal, geometryViewDir, geometryClearcoatNormal, material, reflectedLight );
	}
	#pragma unroll_loop_end
#endif
#if defined( RE_IndirectDiffuse )
	vec3 iblIrradiance = vec3( 0.0 );
	vec3 irradiance = getAmbientLightIrradiance( ambientLightColor );
	#if defined( USE_LIGHT_PROBES )
		irradiance += getLightProbeIrradiance( lightProbe, geometryNormal );
	#endif
	#if ( NUM_HEMI_LIGHTS > 0 )
		#pragma unroll_loop_start
		for ( int i = 0; i < NUM_HEMI_LIGHTS; i ++ ) {
			irradiance += getHemisphereLightIrradiance( hemisphereLights[ i ], geometryNormal );
		}
		#pragma unroll_loop_end
	#endif
	#if NUM_LIGHT_PROBE_GRIDS > 0
		vec3 probeWorldPos = ( ( vec4( geometryPosition, 1.0 ) - viewMatrix[ 3 ] ) * viewMatrix ).xyz;
		vec3 probeWorldNormal = transformNormalByInverseViewMatrix( geometryNormal, viewMatrix );
		int probeGrid = - 1;
		#pragma unroll_loop_start
		for ( int i = 0; i < NUM_LIGHT_PROBE_GRIDS; i ++ ) {
			if ( isInsideLightProbeGrid( probeWorldPos, probeWorldNormal, probesMin[ i ], probesMax[ i ], probesResolution[ i ] ) ) probeGrid = UNROLLED_LOOP_INDEX;
		}
		#pragma unroll_loop_end
		#pragma unroll_loop_start
		for ( int i = 0; i < NUM_LIGHT_PROBE_GRIDS; i ++ ) {
			if ( probeGrid == UNROLLED_LOOP_INDEX ) irradiance += getLightProbeGridIrradiance( probesSH[ i ], probesMin[ i ], probesMax[ i ], probesResolution[ i ], probeWorldPos, probeWorldNormal );
		}
		#pragma unroll_loop_end
	#endif
#endif
#if defined( RE_IndirectSpecular )
	vec3 radiance = vec3( 0.0 );
	vec3 clearcoatRadiance = vec3( 0.0 );
#endif`,tu=`#if defined( RE_IndirectDiffuse )
	#ifdef USE_LIGHTMAP
		vec4 lightMapTexel = texture2D( lightMap, vLightMapUv );
		vec3 lightMapIrradiance = lightMapTexel.rgb * lightMapIntensity;
		irradiance += lightMapIrradiance;
	#endif
	#if defined( USE_ENVMAP ) && defined( ENVMAP_TYPE_PMREM )
		#if defined( STANDARD ) || defined( LAMBERT ) || defined( PHONG )
			iblIrradiance += getIBLIrradiance( geometryNormal );
		#endif
	#endif
#endif
#if defined( USE_ENVMAP ) && defined( RE_IndirectSpecular )
	#ifdef USE_ANISOTROPY
		vec3 iblRadiance = getIBLAnisotropyRadiance( geometryViewDir, geometryNormal, material.roughness, material.anisotropyB, material.anisotropy );
	#else
		vec3 iblRadiance = getIBLRadiance( geometryViewDir, geometryNormal, material.roughness );
	#endif
	#ifdef USE_RETROREFLECTION
		#ifdef USE_ANISOTROPY
			vec3 retroIBLRadiance = getIBLAnisotropyRetroRadiance( geometryViewDir, geometryNormal, material.roughness, material.anisotropyB, material.anisotropy );
		#else
			vec3 retroIBLRadiance = getIBLRetroRadiance( geometryViewDir, geometryNormal, material.roughness );
		#endif
		iblRadiance = mix( iblRadiance, retroIBLRadiance, saturate( material.retroreflectivity ) );
	#endif
	radiance += iblRadiance;
	#ifdef USE_CLEARCOAT
		clearcoatRadiance += getIBLRadiance( geometryViewDir, geometryClearcoatNormal, material.clearcoatRoughness );
	#endif
#endif`,nu=`#if defined( RE_IndirectDiffuse )
	#if defined( LAMBERT ) || defined( PHONG )
		irradiance += iblIrradiance;
	#endif
	RE_IndirectDiffuse( irradiance, geometryPosition, geometryNormal, geometryViewDir, geometryClearcoatNormal, material, reflectedLight );
#endif
#if defined( RE_IndirectSpecular )
	RE_IndirectSpecular( radiance, iblIrradiance, clearcoatRadiance, geometryPosition, geometryNormal, geometryViewDir, geometryClearcoatNormal, material, reflectedLight );
#endif`,iu=`#if NUM_LIGHT_PROBE_GRIDS > 0
uniform highp sampler3D probesSH[ NUM_LIGHT_PROBE_GRIDS ];
uniform vec3 probesMin[ NUM_LIGHT_PROBE_GRIDS ];
uniform vec3 probesMax[ NUM_LIGHT_PROBE_GRIDS ];
uniform vec3 probesResolution[ NUM_LIGHT_PROBE_GRIDS ];
bool isInsideLightProbeGrid( vec3 worldPos, vec3 worldNormal, vec3 probesMin, vec3 probesMax, vec3 probesResolution ) {
	vec3 spacing = ( probesMax - probesMin ) / probesResolution;
	vec3 samplePos = worldPos + worldNormal * spacing * 0.5;
	vec3 margin = spacing * 0.01;
	return all( greaterThanEqual( samplePos, probesMin - margin ) ) && all( lessThanEqual( samplePos, probesMax + margin ) );
}
vec3 getLightProbeGridIrradiance( highp sampler3D probesSH, vec3 probesMin, vec3 probesMax, vec3 probesResolution, vec3 worldPos, vec3 worldNormal ) {
	vec3 res = probesResolution;
	vec3 gridRange = probesMax - probesMin;
	vec3 probeSpacing = gridRange / res;
	vec3 samplePos = worldPos + worldNormal * probeSpacing * 0.5;
	vec3 uvw = clamp( ( samplePos - probesMin ) / gridRange, 0.5 / res, 1.0 - 0.5 / res );
	float nz          = res.z;
	float paddedSlices = nz + 2.0;
	float atlasDepth  = 7.0 * paddedSlices;
	float uvZBase     = uvw.z * nz + 1.0;
	vec4 s0 = texture( probesSH, vec3( uvw.xy, ( uvZBase                       ) / atlasDepth ) );
	vec4 s1 = texture( probesSH, vec3( uvw.xy, ( uvZBase +       paddedSlices   ) / atlasDepth ) );
	vec4 s2 = texture( probesSH, vec3( uvw.xy, ( uvZBase + 2.0 * paddedSlices   ) / atlasDepth ) );
	vec4 s3 = texture( probesSH, vec3( uvw.xy, ( uvZBase + 3.0 * paddedSlices   ) / atlasDepth ) );
	vec4 s4 = texture( probesSH, vec3( uvw.xy, ( uvZBase + 4.0 * paddedSlices   ) / atlasDepth ) );
	vec4 s5 = texture( probesSH, vec3( uvw.xy, ( uvZBase + 5.0 * paddedSlices   ) / atlasDepth ) );
	vec4 s6 = texture( probesSH, vec3( uvw.xy, ( uvZBase + 6.0 * paddedSlices   ) / atlasDepth ) );
	vec3 c0 = s0.xyz;
	vec3 c1 = vec3( s0.w, s1.xy );
	vec3 c2 = vec3( s1.zw, s2.x );
	vec3 c3 = s2.yzw;
	vec3 c4 = s3.xyz;
	vec3 c5 = vec3( s3.w, s4.xy );
	vec3 c6 = vec3( s4.zw, s5.x );
	vec3 c7 = s5.yzw;
	vec3 c8 = s6.xyz;
	float x = worldNormal.x, y = worldNormal.y, z = worldNormal.z;
	vec3 result = c0 * 0.886227;
	result += c1 * 2.0 * 0.511664 * y;
	result += c2 * 2.0 * 0.511664 * z;
	result += c3 * 2.0 * 0.511664 * x;
	result += c4 * 2.0 * 0.429043 * x * y;
	result += c5 * 2.0 * 0.429043 * y * z;
	result += c6 * ( 0.743125 * z * z - 0.247708 );
	result += c7 * 2.0 * 0.429043 * x * z;
	result += c8 * 0.429043 * ( x * x - y * y );
	return max( result, vec3( 0.0 ) );
}
#endif`,su=`#if defined( USE_LOGARITHMIC_DEPTH_BUFFER )
	gl_FragDepth = vIsPerspective == 0.0 ? gl_FragCoord.z : log2( vFragDepth ) * logDepthBufFC * 0.5;
#endif`,ru=`#if defined( USE_LOGARITHMIC_DEPTH_BUFFER )
	uniform float logDepthBufFC;
	varying float vFragDepth;
	varying float vIsPerspective;
#endif`,au=`#ifdef USE_LOGARITHMIC_DEPTH_BUFFER
	varying float vFragDepth;
	varying float vIsPerspective;
#endif`,ou=`#ifdef USE_LOGARITHMIC_DEPTH_BUFFER
	vFragDepth = 1.0 + gl_Position.w;
	vIsPerspective = float( isPerspectiveMatrix( projectionMatrix ) );
#endif`,lu=`#ifdef USE_MAP
	vec4 sampledDiffuseColor = texture2D( map, vMapUv );
	#ifdef DECODE_VIDEO_TEXTURE
		sampledDiffuseColor = sRGBTransferEOTF( sampledDiffuseColor );
	#endif
	diffuseColor *= sampledDiffuseColor;
#endif`,cu=`#ifdef USE_MAP
	uniform sampler2D map;
#endif`,du=`#if defined( USE_MAP ) || defined( USE_ALPHAMAP )
	#if defined( USE_POINTS_UV )
		vec2 uv = vUv;
	#else
		vec2 uv = ( uvTransform * vec3( gl_PointCoord.x, 1.0 - gl_PointCoord.y, 1 ) ).xy;
	#endif
#endif
#ifdef USE_MAP
	diffuseColor *= texture2D( map, uv );
#endif
#ifdef USE_ALPHAMAP
	diffuseColor.a *= texture2D( alphaMap, uv ).g;
#endif`,uu=`#if defined( USE_POINTS_UV )
	varying vec2 vUv;
#else
	#if defined( USE_MAP ) || defined( USE_ALPHAMAP )
		uniform mat3 uvTransform;
	#endif
#endif
#ifdef USE_MAP
	uniform sampler2D map;
#endif
#ifdef USE_ALPHAMAP
	uniform sampler2D alphaMap;
#endif`,fu=`float metalnessFactor = metalness;
#ifdef USE_METALNESSMAP
	vec4 texelMetalness = texture2D( metalnessMap, vMetalnessMapUv );
	metalnessFactor *= texelMetalness.b;
#endif`,hu=`#ifdef USE_METALNESSMAP
	uniform sampler2D metalnessMap;
#endif`,pu=`#ifdef USE_INSTANCING_MORPH
	float morphTargetInfluences[ MORPHTARGETS_COUNT ];
	float morphTargetBaseInfluence = texelFetch( morphTexture, ivec2( 0, gl_InstanceID ), 0 ).r;
	for ( int i = 0; i < MORPHTARGETS_COUNT; i ++ ) {
		morphTargetInfluences[i] =  texelFetch( morphTexture, ivec2( i + 1, gl_InstanceID ), 0 ).r;
	}
#endif`,mu=`#if defined( USE_MORPHCOLORS )
	#if defined( USE_COLOR_ALPHA )
		vColor *= morphTargetBaseInfluence;
	#elif defined( USE_COLOR )
		vColor.rgb *= morphTargetBaseInfluence;
	#endif
	for ( int i = 0; i < MORPHTARGETS_COUNT; i ++ ) {
		#if defined( USE_COLOR_ALPHA )
			if ( morphTargetInfluences[ i ] != 0.0 ) vColor += getMorph( gl_VertexID, i, 2 ) * morphTargetInfluences[ i ];
		#elif defined( USE_COLOR )
			if ( morphTargetInfluences[ i ] != 0.0 ) vColor.rgb += getMorph( gl_VertexID, i, 2 ).rgb * morphTargetInfluences[ i ];
		#endif
	}
#endif`,gu=`#ifdef USE_MORPHNORMALS
	objectNormal *= morphTargetBaseInfluence;
	for ( int i = 0; i < MORPHTARGETS_COUNT; i ++ ) {
		if ( morphTargetInfluences[ i ] != 0.0 ) objectNormal += getMorph( gl_VertexID, i, 1 ).xyz * morphTargetInfluences[ i ];
	}
#endif`,xu=`#ifdef USE_MORPHTARGETS
	#ifndef USE_INSTANCING_MORPH
		uniform float morphTargetBaseInfluence;
		uniform float morphTargetInfluences[ MORPHTARGETS_COUNT ];
	#endif
	uniform sampler2DArray morphTargetsTexture;
	uniform ivec2 morphTargetsTextureSize;
	vec4 getMorph( const in int vertexIndex, const in int morphTargetIndex, const in int offset ) {
		int texelIndex = vertexIndex * MORPHTARGETS_TEXTURE_STRIDE + offset;
		int y = texelIndex / morphTargetsTextureSize.x;
		int x = texelIndex - y * morphTargetsTextureSize.x;
		ivec3 morphUV = ivec3( x, y, morphTargetIndex );
		return texelFetch( morphTargetsTexture, morphUV, 0 );
	}
#endif`,_u=`#ifdef USE_MORPHTARGETS
	transformed *= morphTargetBaseInfluence;
	for ( int i = 0; i < MORPHTARGETS_COUNT; i ++ ) {
		if ( morphTargetInfluences[ i ] != 0.0 ) transformed += getMorph( gl_VertexID, i, 0 ).xyz * morphTargetInfluences[ i ];
	}
#endif`,vu=`float faceDirection = gl_FrontFacing ? 1.0 : - 1.0;
#ifdef FLAT_SHADED
	vec3 fdx = dFdx( vViewPosition );
	vec3 fdy = dFdy( vViewPosition );
	vec3 normal = normalize( cross( fdx, fdy ) );
#else
	vec3 normal = normalize( vNormal );
	#ifdef DOUBLE_SIDED
		normal *= faceDirection;
	#endif
#endif
#if defined( USE_NORMALMAP_TANGENTSPACE ) || defined( USE_CLEARCOAT_NORMALMAP ) || defined( USE_ANISOTROPY )
	#ifdef USE_TANGENT
		mat3 tbn = mat3( normalize( vTangent ), normalize( vBitangent ), normal );
	#else
		mat3 tbn = getTangentFrame( - vViewPosition, normal,
		#if defined( USE_NORMALMAP )
			vNormalMapUv
		#elif defined( USE_CLEARCOAT_NORMALMAP )
			vClearcoatNormalMapUv
		#else
			vUv
		#endif
		);
	#endif
	#ifdef DOUBLE_SIDED
		tbn[0] *= faceDirection;
		tbn[1] *= faceDirection;
	#endif
#endif
#ifdef USE_CLEARCOAT_NORMALMAP
	#ifdef USE_TANGENT
		mat3 tbn2 = mat3( normalize( vTangent ), normalize( vBitangent ), normal );
	#else
		mat3 tbn2 = getTangentFrame( - vViewPosition, normal, vClearcoatNormalMapUv );
	#endif
	#ifdef DOUBLE_SIDED
		tbn2[0] *= faceDirection;
		tbn2[1] *= faceDirection;
	#endif
#endif
vec3 nonPerturbedNormal = normal;`,Su=`#ifdef USE_NORMALMAP_OBJECTSPACE
	normal = texture2D( normalMap, vNormalMapUv ).xyz * 2.0 - 1.0;
	#ifdef FLIP_SIDED
		normal = - normal;
	#endif
	#ifdef DOUBLE_SIDED
		normal = normal * faceDirection;
	#endif
	normal = normalize( normalMatrix * normal );
#elif defined( USE_NORMALMAP_TANGENTSPACE )
	vec3 mapN = texture2D( normalMap, vNormalMapUv ).xyz * 2.0 - 1.0;
	#if defined( USE_PACKED_NORMALMAP )
		mapN = vec3( mapN.xy, sqrt( saturate( 1.0 - dot( mapN.xy, mapN.xy ) ) ) );
	#endif
	mapN.xy *= normalScale;
	normal = normalize( tbn * mapN );
#elif defined( USE_BUMPMAP )
	normal = perturbNormalArb( - vViewPosition, normal, dHdxy_fwd(), faceDirection );
#endif`,Mu=`#ifndef FLAT_SHADED
	varying vec3 vNormal;
	#ifdef USE_TANGENT
		varying vec3 vTangent;
		varying vec3 vBitangent;
	#endif
#endif`,bu=`#ifndef FLAT_SHADED
	varying vec3 vNormal;
	#ifdef USE_TANGENT
		varying vec3 vTangent;
		varying vec3 vBitangent;
	#endif
#endif`,yu=`#ifndef FLAT_SHADED
	vNormal = normalize( transformedNormal );
	#ifdef USE_TANGENT
		vTangent = normalize( transformedTangent );
		vBitangent = normalize( cross( vNormal, vTangent ) * tangent.w );
		#ifdef FLIP_SIDED
			vBitangent = - vBitangent;
		#endif
	#endif
#endif`,Eu=`#ifdef USE_NORMALMAP
	uniform sampler2D normalMap;
	uniform vec2 normalScale;
#endif
#ifdef USE_NORMALMAP_OBJECTSPACE
	uniform mat3 normalMatrix;
#endif
#if ! defined ( USE_TANGENT ) && ( defined ( USE_NORMALMAP_TANGENTSPACE ) || defined ( USE_CLEARCOAT_NORMALMAP ) || defined( USE_ANISOTROPY ) )
	mat3 getTangentFrame( vec3 eye_pos, vec3 surf_norm, vec2 uv ) {
		vec3 q0 = dFdx( eye_pos.xyz );
		vec3 q1 = dFdy( eye_pos.xyz );
		vec2 st0 = dFdx( uv.st );
		vec2 st1 = dFdy( uv.st );
		vec3 N = surf_norm;
		vec3 q1perp = cross( q1, N );
		vec3 q0perp = cross( N, q0 );
		vec3 T = q1perp * st0.x + q0perp * st1.x;
		vec3 B = q1perp * st0.y + q0perp * st1.y;
		float det = max( dot( T, T ), dot( B, B ) );
		float scale = ( det == 0.0 ) ? 0.0 : inversesqrt( det );
		return mat3( T * scale, B * scale, N );
	}
#endif`,Tu=`#ifdef USE_CLEARCOAT
	vec3 clearcoatNormal = nonPerturbedNormal;
#endif`,wu=`#ifdef USE_CLEARCOAT_NORMALMAP
	vec3 clearcoatMapN = texture2D( clearcoatNormalMap, vClearcoatNormalMapUv ).xyz * 2.0 - 1.0;
	clearcoatMapN.xy *= clearcoatNormalScale;
	clearcoatNormal = normalize( tbn2 * clearcoatMapN );
#endif`,Au=`#ifdef USE_CLEARCOATMAP
	uniform sampler2D clearcoatMap;
#endif
#ifdef USE_CLEARCOAT_NORMALMAP
	uniform sampler2D clearcoatNormalMap;
	uniform vec2 clearcoatNormalScale;
#endif
#ifdef USE_CLEARCOAT_ROUGHNESSMAP
	uniform sampler2D clearcoatRoughnessMap;
#endif`,Ru=`#ifdef USE_IRIDESCENCEMAP
	uniform sampler2D iridescenceMap;
#endif
#ifdef USE_IRIDESCENCE_THICKNESSMAP
	uniform sampler2D iridescenceThicknessMap;
#endif`,Cu=`#ifdef OPAQUE
diffuseColor.a = 1.0;
#endif
#ifdef USE_TRANSMISSION
diffuseColor.a *= material.transmissionAlpha;
#endif
gl_FragColor = vec4( outgoingLight, diffuseColor.a );`,Pu=`vec3 packNormalToRGB( const in vec3 normal ) {
	return normalize( normal ) * 0.5 + 0.5;
}
vec3 unpackRGBToNormal( const in vec3 rgb ) {
	return 2.0 * rgb.xyz - 1.0;
}
const float PackUpscale = 256. / 255.;const float UnpackDownscale = 255. / 256.;const float ShiftRight8 = 1. / 256.;
const float Inv255 = 1. / 255.;
const vec4 PackFactors = vec4( 1.0, 256.0, 256.0 * 256.0, 256.0 * 256.0 * 256.0 );
const vec2 UnpackFactors2 = vec2( UnpackDownscale, 1.0 / PackFactors.g );
const vec3 UnpackFactors3 = vec3( UnpackDownscale / PackFactors.rg, 1.0 / PackFactors.b );
const vec4 UnpackFactors4 = vec4( UnpackDownscale / PackFactors.rgb, 1.0 / PackFactors.a );
vec4 packDepthToRGBA( const in float v ) {
	if( v <= 0.0 )
		return vec4( 0., 0., 0., 0. );
	if( v >= 1.0 )
		return vec4( 1., 1., 1., 1. );
	float vuf;
	float af = modf( v * PackFactors.a, vuf );
	float bf = modf( vuf * ShiftRight8, vuf );
	float gf = modf( vuf * ShiftRight8, vuf );
	return vec4( vuf * Inv255, gf * PackUpscale, bf * PackUpscale, af );
}
vec3 packDepthToRGB( const in float v ) {
	if( v <= 0.0 )
		return vec3( 0., 0., 0. );
	if( v >= 1.0 )
		return vec3( 1., 1., 1. );
	float vuf;
	float bf = modf( v * PackFactors.b, vuf );
	float gf = modf( vuf * ShiftRight8, vuf );
	return vec3( vuf * Inv255, gf * PackUpscale, bf );
}
vec2 packDepthToRG( const in float v ) {
	if( v <= 0.0 )
		return vec2( 0., 0. );
	if( v >= 1.0 )
		return vec2( 1., 1. );
	float vuf;
	float gf = modf( v * 256., vuf );
	return vec2( vuf * Inv255, gf );
}
float unpackRGBAToDepth( const in vec4 v ) {
	return dot( v, UnpackFactors4 );
}
float unpackRGBToDepth( const in vec3 v ) {
	return dot( v, UnpackFactors3 );
}
float unpackRGToDepth( const in vec2 v ) {
	return v.r * UnpackFactors2.r + v.g * UnpackFactors2.g;
}
vec4 pack2HalfToRGBA( const in vec2 v ) {
	vec4 r = vec4( v.x, fract( v.x * 255.0 ), v.y, fract( v.y * 255.0 ) );
	return vec4( r.x - r.y / 255.0, r.y, r.z - r.w / 255.0, r.w );
}
vec2 unpackRGBATo2Half( const in vec4 v ) {
	return vec2( v.x + ( v.y / 255.0 ), v.z + ( v.w / 255.0 ) );
}
float viewZToOrthographicDepth( const in float viewZ, const in float near, const in float far ) {
	return ( viewZ + near ) / ( near - far );
}
float orthographicDepthToViewZ( const in float depth, const in float near, const in float far ) {
	#ifdef USE_REVERSED_DEPTH_BUFFER
	
		return depth * ( far - near ) - far;
	#else
		return depth * ( near - far ) - near;
	#endif
}
float viewZToPerspectiveDepth( const in float viewZ, const in float near, const in float far ) {
	return ( ( near + viewZ ) * far ) / ( ( far - near ) * viewZ );
}
float perspectiveDepthToViewZ( const in float depth, const in float near, const in float far ) {
	
	#ifdef USE_REVERSED_DEPTH_BUFFER
		return ( near * far ) / ( ( near - far ) * depth - near );
	#else
		return ( near * far ) / ( ( far - near ) * depth - far );
	#endif
}`,Lu=`#ifdef PREMULTIPLIED_ALPHA
	gl_FragColor.rgb *= gl_FragColor.a;
#endif`,Du=`vec4 mvPosition = vec4( transformed, 1.0 );
#ifdef USE_BATCHING
	mvPosition = batchingMatrix * mvPosition;
#endif
#ifdef USE_INSTANCING
	mvPosition = instanceMatrix * mvPosition;
#endif
mvPosition = modelViewMatrix * mvPosition;
gl_Position = projectionMatrix * mvPosition;`,Iu=`#ifdef DITHERING
	gl_FragColor.rgb = dithering( gl_FragColor.rgb );
#endif`,Nu=`#ifdef DITHERING
	vec3 dithering( vec3 color ) {
		float grid_position = rand( gl_FragCoord.xy );
		vec3 dither_shift_RGB = vec3( 0.25 / 255.0, -0.25 / 255.0, 0.25 / 255.0 );
		dither_shift_RGB = mix( 2.0 * dither_shift_RGB, -2.0 * dither_shift_RGB, grid_position );
		return color + dither_shift_RGB;
	}
#endif`,Uu=`float roughnessFactor = roughness;
#ifdef USE_ROUGHNESSMAP
	vec4 texelRoughness = texture2D( roughnessMap, vRoughnessMapUv );
	roughnessFactor *= texelRoughness.g;
#endif`,Fu=`#ifdef USE_ROUGHNESSMAP
	uniform sampler2D roughnessMap;
#endif`,Ou=`#if NUM_SPOT_LIGHT_COORDS > 0
	varying vec4 vSpotLightCoord[ NUM_SPOT_LIGHT_COORDS ];
#endif
#if NUM_SPOT_LIGHT_MAPS > 0
	uniform sampler2D spotLightMap[ NUM_SPOT_LIGHT_MAPS ];
#endif
#ifdef USE_SHADOWMAP
	#if NUM_SUN_LIGHT_SHADOWS > 0
		#define SUN_LIGHT_CASCADES 2
		#if defined( SHADOWMAP_TYPE_PCF )
			uniform sampler2DShadow sunShadowMap[ NUM_SUN_LIGHT_SHADOWS ];
		#else
			uniform sampler2D sunShadowMap[ NUM_SUN_LIGHT_SHADOWS ];
		#endif
		uniform mat4 sunShadowMatrix[ NUM_SUN_LIGHT_SHADOWS * SUN_LIGHT_CASCADES ];
		uniform vec4 sunShadowCascade[ NUM_SUN_LIGHT_SHADOWS * SUN_LIGHT_CASCADES ];
		varying vec4 vSunShadowWorldPosition;
		varying vec3 vSunShadowWorldNormal;
		struct SunLightShadow {
			float shadowIntensity;
			float shadowBias;
			float shadowNormalBias;
			float shadowRadius;
			vec2 shadowMapSize;
		};
		uniform SunLightShadow sunLightShadows[ NUM_SUN_LIGHT_SHADOWS ];
	#endif
	#if NUM_DIR_LIGHT_SHADOWS > 0
		#if defined( SHADOWMAP_TYPE_PCF )
			uniform sampler2DShadow directionalShadowMap[ NUM_DIR_LIGHT_SHADOWS ];
		#else
			uniform sampler2D directionalShadowMap[ NUM_DIR_LIGHT_SHADOWS ];
		#endif
		varying vec4 vDirectionalShadowCoord[ NUM_DIR_LIGHT_SHADOWS ];
		struct DirectionalLightShadow {
			float shadowIntensity;
			float shadowBias;
			float shadowNormalBias;
			float shadowRadius;
			vec2 shadowMapSize;
		};
		uniform DirectionalLightShadow directionalLightShadows[ NUM_DIR_LIGHT_SHADOWS ];
	#endif
	#if NUM_SPOT_LIGHT_SHADOWS > 0
		#if defined( SHADOWMAP_TYPE_PCF )
			uniform sampler2DShadow spotShadowMap[ NUM_SPOT_LIGHT_SHADOWS ];
		#else
			uniform sampler2D spotShadowMap[ NUM_SPOT_LIGHT_SHADOWS ];
		#endif
		struct SpotLightShadow {
			float shadowIntensity;
			float shadowBias;
			float shadowNormalBias;
			float shadowRadius;
			vec2 shadowMapSize;
		};
		uniform SpotLightShadow spotLightShadows[ NUM_SPOT_LIGHT_SHADOWS ];
	#endif
	#if NUM_POINT_LIGHT_SHADOWS > 0
		#if defined( SHADOWMAP_TYPE_PCF )
			uniform samplerCubeShadow pointShadowMap[ NUM_POINT_LIGHT_SHADOWS ];
		#elif defined( SHADOWMAP_TYPE_BASIC )
			uniform samplerCube pointShadowMap[ NUM_POINT_LIGHT_SHADOWS ];
		#endif
		varying vec4 vPointShadowCoord[ NUM_POINT_LIGHT_SHADOWS ];
		struct PointLightShadow {
			float shadowIntensity;
			float shadowBias;
			float shadowNormalBias;
			float shadowRadius;
			vec2 shadowMapSize;
			float shadowCameraNear;
			float shadowCameraFar;
		};
		uniform PointLightShadow pointLightShadows[ NUM_POINT_LIGHT_SHADOWS ];
	#endif
	#if defined( SHADOWMAP_TYPE_PCF )
		float interleavedGradientNoise( vec2 position ) {
			return fract( 52.9829189 * fract( dot( position, vec2( 0.06711056, 0.00583715 ) ) ) );
		}
		vec2 vogelDiskSample( int sampleIndex, int samplesCount, float phi ) {
			const float goldenAngle = 2.399963229728653;
			float r = sqrt( ( float( sampleIndex ) + 0.5 ) / float( samplesCount ) );
			float theta = float( sampleIndex ) * goldenAngle + phi;
			return vec2( cos( theta ), sin( theta ) ) * r;
		}
	#endif
	#if defined( SHADOWMAP_TYPE_PCF )
		float getShadow( sampler2DShadow shadowMap, vec2 shadowMapSize, float shadowIntensity, float shadowBias, float shadowRadius, vec4 shadowCoord ) {
			float shadow = 1.0;
			shadowCoord.xyz /= shadowCoord.w;
			#ifdef USE_REVERSED_DEPTH_BUFFER
				shadowCoord.z -= shadowBias;
			#else
				shadowCoord.z += shadowBias;
			#endif
			bool inFrustum = shadowCoord.x >= 0.0 && shadowCoord.x <= 1.0 && shadowCoord.y >= 0.0 && shadowCoord.y <= 1.0;
			bool frustumTest = inFrustum && shadowCoord.z <= 1.0;
			if ( frustumTest ) {
				vec2 texelSize = vec2( 1.0 ) / shadowMapSize;
				vec2 radius = shadowRadius * texelSize;
				float phi = interleavedGradientNoise( gl_FragCoord.xy ) * PI2;
				shadow = (
					texture( shadowMap, vec3( shadowCoord.xy + vogelDiskSample( 0, 5, phi ) * radius, shadowCoord.z ) ) +
					texture( shadowMap, vec3( shadowCoord.xy + vogelDiskSample( 1, 5, phi ) * radius, shadowCoord.z ) ) +
					texture( shadowMap, vec3( shadowCoord.xy + vogelDiskSample( 2, 5, phi ) * radius, shadowCoord.z ) ) +
					texture( shadowMap, vec3( shadowCoord.xy + vogelDiskSample( 3, 5, phi ) * radius, shadowCoord.z ) ) +
					texture( shadowMap, vec3( shadowCoord.xy + vogelDiskSample( 4, 5, phi ) * radius, shadowCoord.z ) )
				) * 0.2;
			}
			return mix( 1.0, shadow, shadowIntensity );
		}
	#elif defined( SHADOWMAP_TYPE_VSM )
		float getShadow( sampler2D shadowMap, vec2 shadowMapSize, float shadowIntensity, float shadowBias, float shadowRadius, vec4 shadowCoord ) {
			float shadow = 1.0;
			shadowCoord.xyz /= shadowCoord.w;
			#ifdef USE_REVERSED_DEPTH_BUFFER
				shadowCoord.z -= shadowBias;
			#else
				shadowCoord.z += shadowBias;
			#endif
			bool inFrustum = shadowCoord.x >= 0.0 && shadowCoord.x <= 1.0 && shadowCoord.y >= 0.0 && shadowCoord.y <= 1.0;
			bool frustumTest = inFrustum && shadowCoord.z <= 1.0;
			if ( frustumTest ) {
				vec2 distribution = texture2D( shadowMap, shadowCoord.xy ).rg;
				float mean = distribution.x;
				float variance = distribution.y * distribution.y;
				#ifdef USE_REVERSED_DEPTH_BUFFER
					float hard_shadow = step( mean, shadowCoord.z );
				#else
					float hard_shadow = step( shadowCoord.z, mean );
				#endif
				
				if ( hard_shadow == 1.0 ) {
					shadow = 1.0;
				} else {
					variance = max( variance, 0.0000001 );
					float d = shadowCoord.z - mean;
					float p_max = variance / ( variance + d * d );
					p_max = clamp( ( p_max - 0.3 ) / 0.65, 0.0, 1.0 );
					shadow = max( hard_shadow, p_max );
				}
			}
			return mix( 1.0, shadow, shadowIntensity );
		}
	#else
		float getShadow( sampler2D shadowMap, vec2 shadowMapSize, float shadowIntensity, float shadowBias, float shadowRadius, vec4 shadowCoord ) {
			float shadow = 1.0;
			shadowCoord.xyz /= shadowCoord.w;
			#ifdef USE_REVERSED_DEPTH_BUFFER
				shadowCoord.z -= shadowBias;
			#else
				shadowCoord.z += shadowBias;
			#endif
			bool inFrustum = shadowCoord.x >= 0.0 && shadowCoord.x <= 1.0 && shadowCoord.y >= 0.0 && shadowCoord.y <= 1.0;
			bool frustumTest = inFrustum && shadowCoord.z <= 1.0;
			if ( frustumTest ) {
				float depth = texture2D( shadowMap, shadowCoord.xy ).r;
				#ifdef USE_REVERSED_DEPTH_BUFFER
					shadow = step( depth, shadowCoord.z );
				#else
					shadow = step( shadowCoord.z, depth );
				#endif
			}
			return mix( 1.0, shadow, shadowIntensity );
		}
	#endif
	#if NUM_SUN_LIGHT_SHADOWS > 0
		float getSunShadow(
			#if defined( SHADOWMAP_TYPE_PCF )
				sampler2DShadow shadowMap,
			#else
				sampler2D shadowMap,
			#endif
			SunLightShadow sunLightShadow,
			int shadowIndex
		) {
			vec4 shadowWorldPosition = vec4( vSunShadowWorldPosition.xyz + vSunShadowWorldNormal * sunLightShadow.shadowNormalBias, 1.0 );
			float viewDepth = vSunShadowWorldPosition.w;
			int cascadeOffset = shadowIndex * SUN_LIGHT_CASCADES;
			float shadow = 1.0;
			for ( int i = SUN_LIGHT_CASCADES - 1; i >= 0; i -- ) {
				vec4 cascade = sunShadowCascade[ cascadeOffset + i ];
				if ( viewDepth >= cascade.x && viewDepth < cascade.y ) {
					float cascadeShadow = getShadow(
						shadowMap,
						sunLightShadow.shadowMapSize,
						sunLightShadow.shadowIntensity,
						sunLightShadow.shadowBias,
						sunLightShadow.shadowRadius,
						sunShadowMatrix[ cascadeOffset + i ] * shadowWorldPosition
					);
					shadow = mix( cascadeShadow, shadow, smoothstep( cascade.z, cascade.y, viewDepth ) );
				}
			}
			return shadow;
		}
	#endif
	#if NUM_POINT_LIGHT_SHADOWS > 0
	#if defined( SHADOWMAP_TYPE_PCF )
	float getPointShadow( samplerCubeShadow shadowMap, vec2 shadowMapSize, float shadowIntensity, float shadowBias, float shadowRadius, vec4 shadowCoord, float shadowCameraNear, float shadowCameraFar ) {
		float shadow = 1.0;
		vec3 lightToPosition = shadowCoord.xyz;
		vec3 bd3D = normalize( lightToPosition );
		vec3 absVec = abs( lightToPosition );
		float viewSpaceZ = max( max( absVec.x, absVec.y ), absVec.z );
		if ( viewSpaceZ - shadowCameraFar <= 0.0 && viewSpaceZ - shadowCameraNear >= 0.0 ) {
			float lightDistance = length( lightToPosition );
			float depthRange = shadowCameraFar - shadowCameraNear;
			#ifdef USE_REVERSED_DEPTH_BUFFER
				float depthA = - shadowCameraNear / depthRange - shadowBias;
				float depthB = shadowCameraNear * shadowCameraFar / ( depthRange * lightDistance );
			#else
				float depthA = shadowCameraFar / depthRange + shadowBias;
				float depthB = - shadowCameraNear * shadowCameraFar / ( depthRange * lightDistance );
			#endif
			float texelSize = shadowRadius / shadowMapSize.x;
			vec3 absDir = abs( bd3D );
			vec3 tangent = absDir.x > absDir.z ? vec3( 0.0, 1.0, 0.0 ) : vec3( 1.0, 0.0, 0.0 );
			tangent = normalize( cross( bd3D, tangent ) );
			vec3 bitangent = cross( bd3D, tangent );
			float phi = interleavedGradientNoise( gl_FragCoord.xy ) * PI2;
			shadow = 0.0;
			for ( int i = 0; i < 5; i ++ ) {
				vec2 offset = vogelDiskSample( i, 5, phi );
				vec3 direction = bd3D + ( tangent * offset.x + bitangent * offset.y ) * texelSize;
				vec3 absDirection = abs( direction );
				float dp = depthA + depthB / max( max( absDirection.x, absDirection.y ), absDirection.z );
				shadow += texture( shadowMap, vec4( direction, dp ) );
			}
			shadow *= 0.2;
		}
		return mix( 1.0, shadow, shadowIntensity );
	}
	#elif defined( SHADOWMAP_TYPE_BASIC )
	float getPointShadow( samplerCube shadowMap, vec2 shadowMapSize, float shadowIntensity, float shadowBias, float shadowRadius, vec4 shadowCoord, float shadowCameraNear, float shadowCameraFar ) {
		float shadow = 1.0;
		vec3 lightToPosition = shadowCoord.xyz;
		vec3 absVec = abs( lightToPosition );
		float viewSpaceZ = max( max( absVec.x, absVec.y ), absVec.z );
		if ( viewSpaceZ - shadowCameraFar <= 0.0 && viewSpaceZ - shadowCameraNear >= 0.0 ) {
			float dp = ( shadowCameraFar * ( viewSpaceZ - shadowCameraNear ) ) / ( viewSpaceZ * ( shadowCameraFar - shadowCameraNear ) );
			dp += shadowBias;
			vec3 bd3D = normalize( lightToPosition );
			float depth = textureCube( shadowMap, bd3D ).r;
			#ifdef USE_REVERSED_DEPTH_BUFFER
				depth = 1.0 - depth;
			#endif
			shadow = step( dp, depth );
		}
		return mix( 1.0, shadow, shadowIntensity );
	}
	#endif
	#endif
#endif`,Bu=`#if NUM_SPOT_LIGHT_COORDS > 0
	uniform mat4 spotLightMatrix[ NUM_SPOT_LIGHT_COORDS ];
	varying vec4 vSpotLightCoord[ NUM_SPOT_LIGHT_COORDS ];
#endif
#ifdef USE_SHADOWMAP
	#if NUM_SUN_LIGHT_SHADOWS > 0
		varying vec4 vSunShadowWorldPosition;
		varying vec3 vSunShadowWorldNormal;
	#endif
	#if NUM_DIR_LIGHT_SHADOWS > 0
		uniform mat4 directionalShadowMatrix[ NUM_DIR_LIGHT_SHADOWS ];
		varying vec4 vDirectionalShadowCoord[ NUM_DIR_LIGHT_SHADOWS ];
		struct DirectionalLightShadow {
			float shadowIntensity;
			float shadowBias;
			float shadowNormalBias;
			float shadowRadius;
			vec2 shadowMapSize;
		};
		uniform DirectionalLightShadow directionalLightShadows[ NUM_DIR_LIGHT_SHADOWS ];
	#endif
	#if NUM_SPOT_LIGHT_SHADOWS > 0
		struct SpotLightShadow {
			float shadowIntensity;
			float shadowBias;
			float shadowNormalBias;
			float shadowRadius;
			vec2 shadowMapSize;
		};
		uniform SpotLightShadow spotLightShadows[ NUM_SPOT_LIGHT_SHADOWS ];
	#endif
	#if NUM_POINT_LIGHT_SHADOWS > 0
		uniform mat4 pointShadowMatrix[ NUM_POINT_LIGHT_SHADOWS ];
		varying vec4 vPointShadowCoord[ NUM_POINT_LIGHT_SHADOWS ];
		struct PointLightShadow {
			float shadowIntensity;
			float shadowBias;
			float shadowNormalBias;
			float shadowRadius;
			vec2 shadowMapSize;
			float shadowCameraNear;
			float shadowCameraFar;
		};
		uniform PointLightShadow pointLightShadows[ NUM_POINT_LIGHT_SHADOWS ];
	#endif
#endif`,ku=`#if ( defined( USE_SHADOWMAP ) && ( NUM_DIR_LIGHT_SHADOWS > 0 || NUM_SUN_LIGHT_SHADOWS > 0 || NUM_POINT_LIGHT_SHADOWS > 0 ) ) || ( NUM_SPOT_LIGHT_COORDS > 0 )
	#ifdef HAS_NORMAL
		vec3 shadowWorldNormal = transformNormalByInverseViewMatrix( transformedNormal, viewMatrix );
	#else
		vec3 shadowWorldNormal = vec3( 0.0 );
	#endif
	vec4 shadowWorldPosition;
#endif
#if defined( USE_SHADOWMAP )
	#if NUM_SUN_LIGHT_SHADOWS > 0
		vSunShadowWorldPosition = vec4( worldPosition.xyz, - mvPosition.z );
		vSunShadowWorldNormal = shadowWorldNormal;
	#endif
	#if NUM_DIR_LIGHT_SHADOWS > 0
		#pragma unroll_loop_start
		for ( int i = 0; i < NUM_DIR_LIGHT_SHADOWS; i ++ ) {
			shadowWorldPosition = worldPosition + vec4( shadowWorldNormal * directionalLightShadows[ i ].shadowNormalBias, 0 );
			vDirectionalShadowCoord[ i ] = directionalShadowMatrix[ i ] * shadowWorldPosition;
		}
		#pragma unroll_loop_end
	#endif
	#if NUM_POINT_LIGHT_SHADOWS > 0
		#pragma unroll_loop_start
		for ( int i = 0; i < NUM_POINT_LIGHT_SHADOWS; i ++ ) {
			shadowWorldPosition = worldPosition + vec4( shadowWorldNormal * pointLightShadows[ i ].shadowNormalBias, 0 );
			vPointShadowCoord[ i ] = pointShadowMatrix[ i ] * shadowWorldPosition;
		}
		#pragma unroll_loop_end
	#endif
#endif
#if NUM_SPOT_LIGHT_COORDS > 0
	#pragma unroll_loop_start
	for ( int i = 0; i < NUM_SPOT_LIGHT_COORDS; i ++ ) {
		shadowWorldPosition = worldPosition;
		#if ( defined( USE_SHADOWMAP ) && UNROLLED_LOOP_INDEX < NUM_SPOT_LIGHT_SHADOWS )
			shadowWorldPosition.xyz += shadowWorldNormal * spotLightShadows[ i ].shadowNormalBias;
		#endif
		vSpotLightCoord[ i ] = spotLightMatrix[ i ] * shadowWorldPosition;
	}
	#pragma unroll_loop_end
#endif`,zu=`float getShadowMask() {
	float shadow = 1.0;
	#ifdef USE_SHADOWMAP
	#if NUM_SUN_LIGHT_SHADOWS > 0
	SunLightShadow sunLight;
	#pragma unroll_loop_start
	for ( int i = 0; i < NUM_SUN_LIGHT_SHADOWS; i ++ ) {
		sunLight = sunLightShadows[ i ];
		shadow *= receiveShadow ? getSunShadow( sunShadowMap[ i ], sunLight, UNROLLED_LOOP_INDEX ) : 1.0;
	}
	#pragma unroll_loop_end
	#endif
	#if NUM_DIR_LIGHT_SHADOWS > 0
	DirectionalLightShadow directionalLight;
	#pragma unroll_loop_start
	for ( int i = 0; i < NUM_DIR_LIGHT_SHADOWS; i ++ ) {
		directionalLight = directionalLightShadows[ i ];
		shadow *= receiveShadow ? getShadow( directionalShadowMap[ i ], directionalLight.shadowMapSize, directionalLight.shadowIntensity, directionalLight.shadowBias, directionalLight.shadowRadius, vDirectionalShadowCoord[ i ] ) : 1.0;
	}
	#pragma unroll_loop_end
	#endif
	#if NUM_SPOT_LIGHT_SHADOWS > 0
	SpotLightShadow spotLight;
	#pragma unroll_loop_start
	for ( int i = 0; i < NUM_SPOT_LIGHT_SHADOWS; i ++ ) {
		spotLight = spotLightShadows[ i ];
		shadow *= receiveShadow ? getShadow( spotShadowMap[ i ], spotLight.shadowMapSize, spotLight.shadowIntensity, spotLight.shadowBias, spotLight.shadowRadius, vSpotLightCoord[ i ] ) : 1.0;
	}
	#pragma unroll_loop_end
	#endif
	#if NUM_POINT_LIGHT_SHADOWS > 0 && ( defined( SHADOWMAP_TYPE_PCF ) || defined( SHADOWMAP_TYPE_BASIC ) )
	PointLightShadow pointLight;
	#pragma unroll_loop_start
	for ( int i = 0; i < NUM_POINT_LIGHT_SHADOWS; i ++ ) {
		pointLight = pointLightShadows[ i ];
		shadow *= receiveShadow ? getPointShadow( pointShadowMap[ i ], pointLight.shadowMapSize, pointLight.shadowIntensity, pointLight.shadowBias, pointLight.shadowRadius, vPointShadowCoord[ i ], pointLight.shadowCameraNear, pointLight.shadowCameraFar ) : 1.0;
	}
	#pragma unroll_loop_end
	#endif
	#endif
	return shadow;
}`,Gu=`#ifdef USE_SKINNING
	mat4 boneMatX = getBoneMatrix( skinIndex.x );
	mat4 boneMatY = getBoneMatrix( skinIndex.y );
	mat4 boneMatZ = getBoneMatrix( skinIndex.z );
	mat4 boneMatW = getBoneMatrix( skinIndex.w );
#endif`,Hu=`#ifdef USE_SKINNING
	uniform mat4 bindMatrix;
	uniform mat4 bindMatrixInverse;
	uniform highp sampler2D boneTexture;
	mat4 getBoneMatrix( const in float i ) {
		int size = textureSize( boneTexture, 0 ).x;
		int j = int( i ) * 4;
		int x = j % size;
		int y = j / size;
		vec4 v1 = texelFetch( boneTexture, ivec2( x, y ), 0 );
		vec4 v2 = texelFetch( boneTexture, ivec2( x + 1, y ), 0 );
		vec4 v3 = texelFetch( boneTexture, ivec2( x + 2, y ), 0 );
		vec4 v4 = texelFetch( boneTexture, ivec2( x + 3, y ), 0 );
		return mat4( v1, v2, v3, v4 );
	}
#endif`,Vu=`#ifdef USE_SKINNING
	vec4 skinVertex = bindMatrix * vec4( transformed, 1.0 );
	vec4 skinned = vec4( 0.0 );
	skinned += boneMatX * skinVertex * skinWeight.x;
	skinned += boneMatY * skinVertex * skinWeight.y;
	skinned += boneMatZ * skinVertex * skinWeight.z;
	skinned += boneMatW * skinVertex * skinWeight.w;
	transformed = ( bindMatrixInverse * skinned ).xyz;
#endif`,Wu=`#ifdef USE_SKINNING
	mat4 skinMatrix = mat4( 0.0 );
	skinMatrix += skinWeight.x * boneMatX;
	skinMatrix += skinWeight.y * boneMatY;
	skinMatrix += skinWeight.z * boneMatZ;
	skinMatrix += skinWeight.w * boneMatW;
	skinMatrix = bindMatrixInverse * skinMatrix * bindMatrix;
	objectNormal = vec4( skinMatrix * vec4( objectNormal, 0.0 ) ).xyz;
	#ifdef USE_TANGENT
		objectTangent = vec4( skinMatrix * vec4( objectTangent, 0.0 ) ).xyz;
	#endif
#endif`,Xu=`float specularStrength;
#ifdef USE_SPECULARMAP
	vec4 texelSpecular = texture2D( specularMap, vSpecularMapUv );
	specularStrength = texelSpecular.r;
#else
	specularStrength = 1.0;
#endif`,qu=`#ifdef USE_SPECULARMAP
	uniform sampler2D specularMap;
#endif`,Yu=`#if defined( TONE_MAPPING )
	gl_FragColor.rgb = toneMapping( gl_FragColor.rgb );
#endif`,Ku=`#ifndef saturate
#define saturate( a ) clamp( a, 0.0, 1.0 )
#endif
uniform float toneMappingExposure;
vec3 LinearToneMapping( vec3 color ) {
	return saturate( toneMappingExposure * color );
}
vec3 ReinhardToneMapping( vec3 color ) {
	color *= toneMappingExposure;
	return saturate( color / ( vec3( 1.0 ) + color ) );
}
vec3 CineonToneMapping( vec3 color ) {
	color *= toneMappingExposure;
	color = max( vec3( 0.0 ), color - 0.004 );
	return pow( ( color * ( 6.2 * color + 0.5 ) ) / ( color * ( 6.2 * color + 1.7 ) + 0.06 ), vec3( 2.2 ) );
}
vec3 RRTAndODTFit( vec3 v ) {
	vec3 a = v * ( v + 0.0245786 ) - 0.000090537;
	vec3 b = v * ( 0.983729 * v + 0.4329510 ) + 0.238081;
	return a / b;
}
vec3 ACESFilmicToneMapping( vec3 color ) {
	const mat3 ACESInputMat = mat3(
		vec3( 0.59719, 0.07600, 0.02840 ),		vec3( 0.35458, 0.90834, 0.13383 ),
		vec3( 0.04823, 0.01566, 0.83777 )
	);
	const mat3 ACESOutputMat = mat3(
		vec3(  1.60475, -0.10208, -0.00327 ),		vec3( -0.53108,  1.10813, -0.07276 ),
		vec3( -0.07367, -0.00605,  1.07602 )
	);
	color *= toneMappingExposure / 0.6;
	color = ACESInputMat * color;
	color = RRTAndODTFit( color );
	color = ACESOutputMat * color;
	return saturate( color );
}
const mat3 LINEAR_REC2020_TO_LINEAR_SRGB = mat3(
	vec3( 1.6605, - 0.1246, - 0.0182 ),
	vec3( - 0.5876, 1.1329, - 0.1006 ),
	vec3( - 0.0728, - 0.0083, 1.1187 )
);
const mat3 LINEAR_SRGB_TO_LINEAR_REC2020 = mat3(
	vec3( 0.6274, 0.0691, 0.0164 ),
	vec3( 0.3293, 0.9195, 0.0880 ),
	vec3( 0.0433, 0.0113, 0.8956 )
);
vec3 agxDefaultContrastApprox( vec3 x ) {
	vec3 x2 = x * x;
	vec3 x4 = x2 * x2;
	return + 15.5 * x4 * x2
		- 40.14 * x4 * x
		+ 31.96 * x4
		- 6.868 * x2 * x
		+ 0.4298 * x2
		+ 0.1191 * x
		- 0.00232;
}
vec3 AgXToneMapping( vec3 color ) {
	const mat3 AgXInsetMatrix = mat3(
		vec3( 0.856627153315983, 0.137318972929847, 0.11189821299995 ),
		vec3( 0.0951212405381588, 0.761241990602591, 0.0767994186031903 ),
		vec3( 0.0482516061458583, 0.101439036467562, 0.811302368396859 )
	);
	const mat3 AgXOutsetMatrix = mat3(
		vec3( 1.1271005818144368, - 0.1413297634984383, - 0.14132976349843826 ),
		vec3( - 0.11060664309660323, 1.157823702216272, - 0.11060664309660294 ),
		vec3( - 0.016493938717834573, - 0.016493938717834257, 1.2519364065950405 )
	);
	const float AgxMinEv = - 12.47393;	const float AgxMaxEv = 4.026069;
	color *= toneMappingExposure;
	color = LINEAR_SRGB_TO_LINEAR_REC2020 * color;
	color = AgXInsetMatrix * color;
	color = max( color, 1e-10 );	color = log2( color );
	color = ( color - AgxMinEv ) / ( AgxMaxEv - AgxMinEv );
	color = clamp( color, 0.0, 1.0 );
	color = agxDefaultContrastApprox( color );
	color = AgXOutsetMatrix * color;
	color = pow( max( vec3( 0.0 ), color ), vec3( 2.2 ) );
	color = LINEAR_REC2020_TO_LINEAR_SRGB * color;
	color = clamp( color, 0.0, 1.0 );
	return color;
}
vec3 NeutralToneMapping( vec3 color ) {
	const float StartCompression = 0.8 - 0.04;
	const float Desaturation = 0.15;
	color *= toneMappingExposure;
	float x = min( color.r, min( color.g, color.b ) );
	float offset = x < 0.08 ? x - 6.25 * x * x : 0.04;
	color -= offset;
	float peak = max( color.r, max( color.g, color.b ) );
	if ( peak < StartCompression ) return color;
	float d = 1. - StartCompression;
	float newPeak = 1. - d * d / ( peak + d - StartCompression );
	color *= newPeak / peak;
	float g = 1. - 1. / ( Desaturation * ( peak - newPeak ) + 1. );
	return mix( color, vec3( newPeak ), g );
}
vec3 CustomToneMapping( vec3 color ) { return color; }`,Zu=`#ifdef USE_TRANSMISSION
	material.transmission = transmission;
	material.transmissionAlpha = 1.0;
	material.thickness = thickness;
	material.attenuationDistance = attenuationDistance;
	material.attenuationColor = attenuationColor;
	#ifdef USE_TRANSMISSIONMAP
		material.transmission *= texture2D( transmissionMap, vTransmissionMapUv ).r;
	#endif
	#ifdef USE_THICKNESSMAP
		material.thickness *= texture2D( thicknessMap, vThicknessMapUv ).g;
	#endif
	vec3 pos = vWorldPosition;
	vec3 v = normalize( cameraPosition - pos );
	vec3 n = transformNormalByInverseViewMatrix( normal, viewMatrix );
	vec4 transmitted = getIBLVolumeRefraction(
		n, v, material.roughness, material.diffuseContribution, material.specularColorBlended, material.specularF90,
		pos, modelMatrix, viewMatrix, projectionMatrix, material.dispersion, material.ior, material.thickness,
		material.attenuationColor, material.attenuationDistance );
	material.transmissionAlpha = mix( material.transmissionAlpha, transmitted.a, material.transmission );
	totalDiffuse = mix( totalDiffuse, transmitted.rgb, material.transmission );
#endif`,$u=`#ifdef USE_TRANSMISSION
	uniform float transmission;
	uniform float thickness;
	uniform float attenuationDistance;
	uniform vec3 attenuationColor;
	#ifdef USE_TRANSMISSIONMAP
		uniform sampler2D transmissionMap;
	#endif
	#ifdef USE_THICKNESSMAP
		uniform sampler2D thicknessMap;
	#endif
	uniform vec2 transmissionSamplerSize;
	uniform sampler2D transmissionSamplerMap;
	uniform mat4 modelMatrix;
	uniform mat4 projectionMatrix;
	varying vec3 vWorldPosition;
	float w0( float a ) {
		return ( 1.0 / 6.0 ) * ( a * ( a * ( - a + 3.0 ) - 3.0 ) + 1.0 );
	}
	float w1( float a ) {
		return ( 1.0 / 6.0 ) * ( a *  a * ( 3.0 * a - 6.0 ) + 4.0 );
	}
	float w2( float a ){
		return ( 1.0 / 6.0 ) * ( a * ( a * ( - 3.0 * a + 3.0 ) + 3.0 ) + 1.0 );
	}
	float w3( float a ) {
		return ( 1.0 / 6.0 ) * ( a * a * a );
	}
	float g0( float a ) {
		return w0( a ) + w1( a );
	}
	float g1( float a ) {
		return w2( a ) + w3( a );
	}
	float h0( float a ) {
		return - 1.0 + w1( a ) / ( w0( a ) + w1( a ) );
	}
	float h1( float a ) {
		return 1.0 + w3( a ) / ( w2( a ) + w3( a ) );
	}
	vec4 bicubic( sampler2D tex, vec2 uv, vec4 texelSize, float lod ) {
		uv = uv * texelSize.zw + 0.5;
		vec2 iuv = floor( uv );
		vec2 fuv = fract( uv );
		float g0x = g0( fuv.x );
		float g1x = g1( fuv.x );
		float h0x = h0( fuv.x );
		float h1x = h1( fuv.x );
		float h0y = h0( fuv.y );
		float h1y = h1( fuv.y );
		vec2 p0 = ( vec2( iuv.x + h0x, iuv.y + h0y ) - 0.5 ) * texelSize.xy;
		vec2 p1 = ( vec2( iuv.x + h1x, iuv.y + h0y ) - 0.5 ) * texelSize.xy;
		vec2 p2 = ( vec2( iuv.x + h0x, iuv.y + h1y ) - 0.5 ) * texelSize.xy;
		vec2 p3 = ( vec2( iuv.x + h1x, iuv.y + h1y ) - 0.5 ) * texelSize.xy;
		return g0( fuv.y ) * ( g0x * textureLod( tex, p0, lod ) + g1x * textureLod( tex, p1, lod ) ) +
			g1( fuv.y ) * ( g0x * textureLod( tex, p2, lod ) + g1x * textureLod( tex, p3, lod ) );
	}
	vec4 textureBicubic( sampler2D sampler, vec2 uv, float lod ) {
		vec2 fLodSize = vec2( textureSize( sampler, int( lod ) ) );
		vec2 cLodSize = vec2( textureSize( sampler, int( lod + 1.0 ) ) );
		vec2 fLodSizeInv = 1.0 / fLodSize;
		vec2 cLodSizeInv = 1.0 / cLodSize;
		vec4 fSample = bicubic( sampler, uv, vec4( fLodSizeInv, fLodSize ), floor( lod ) );
		vec4 cSample = bicubic( sampler, uv, vec4( cLodSizeInv, cLodSize ), ceil( lod ) );
		return mix( fSample, cSample, fract( lod ) );
	}
	vec3 getVolumeTransmissionRay( const in vec3 n, const in vec3 v, const in float thickness, const in float ior, const in mat4 modelMatrix ) {
		vec3 refractionVector = refract( - v, normalize( n ), 1.0 / ior );
		vec3 modelScale;
		modelScale.x = length( vec3( modelMatrix[ 0 ].xyz ) );
		modelScale.y = length( vec3( modelMatrix[ 1 ].xyz ) );
		modelScale.z = length( vec3( modelMatrix[ 2 ].xyz ) );
		return normalize( refractionVector ) * thickness * modelScale;
	}
	float applyIorToRoughness( const in float roughness, const in float ior ) {
		return roughness * clamp( ior * 2.0 - 2.0, 0.0, 1.0 );
	}
	vec4 getTransmissionSample( const in vec2 fragCoord, const in float roughness, const in float ior ) {
		float lod = log2( transmissionSamplerSize.x ) * applyIorToRoughness( roughness, ior );
		return textureBicubic( transmissionSamplerMap, fragCoord.xy, lod );
	}
	vec3 volumeAttenuation( const in float transmissionDistance, const in vec3 attenuationColor, const in float attenuationDistance ) {
		if ( isinf( attenuationDistance ) ) {
			return vec3( 1.0 );
		} else {
			vec3 attenuationCoefficient = -log( attenuationColor ) / attenuationDistance;
			vec3 transmittance = exp( - attenuationCoefficient * transmissionDistance );			return transmittance;
		}
	}
	vec4 getIBLVolumeRefraction( const in vec3 n, const in vec3 v, const in float roughness, const in vec3 diffuseColor,
		const in vec3 specularColor, const in float specularF90, const in vec3 position, const in mat4 modelMatrix,
		const in mat4 viewMatrix, const in mat4 projMatrix, const in float dispersion, const in float ior, const in float thickness,
		const in vec3 attenuationColor, const in float attenuationDistance ) {
		vec4 transmittedLight;
		vec3 transmittance;
		#ifdef USE_DISPERSION
			float halfSpread = ( ior - 1.0 ) * 0.025 * dispersion;
			vec3 iors = vec3( ior - halfSpread, ior, ior + halfSpread );
			for ( int i = 0; i < 3; i ++ ) {
				vec3 transmissionRay = getVolumeTransmissionRay( n, v, thickness, iors[ i ], modelMatrix );
				vec3 refractedRayExit = position + transmissionRay;
				vec4 ndcPos = projMatrix * viewMatrix * vec4( refractedRayExit, 1.0 );
				vec2 refractionCoords = ndcPos.xy / ndcPos.w;
				refractionCoords += 1.0;
				refractionCoords /= 2.0;
				vec4 transmissionSample = getTransmissionSample( refractionCoords, roughness, iors[ i ] );
				transmittedLight[ i ] = transmissionSample[ i ];
				transmittedLight.a += transmissionSample.a;
				transmittance[ i ] = diffuseColor[ i ] * volumeAttenuation( length( transmissionRay ), attenuationColor, attenuationDistance )[ i ];
			}
			transmittedLight.a /= 3.0;
		#else
			vec3 transmissionRay = getVolumeTransmissionRay( n, v, thickness, ior, modelMatrix );
			vec3 refractedRayExit = position + transmissionRay;
			vec4 ndcPos = projMatrix * viewMatrix * vec4( refractedRayExit, 1.0 );
			vec2 refractionCoords = ndcPos.xy / ndcPos.w;
			refractionCoords += 1.0;
			refractionCoords /= 2.0;
			transmittedLight = getTransmissionSample( refractionCoords, roughness, ior );
			transmittance = diffuseColor * volumeAttenuation( length( transmissionRay ), attenuationColor, attenuationDistance );
		#endif
		vec3 attenuatedColor = transmittance * transmittedLight.rgb;
		vec3 F = EnvironmentBRDF( n, v, specularColor, specularF90, roughness );
		float transmittanceFactor = ( transmittance.r + transmittance.g + transmittance.b ) / 3.0;
		return vec4( ( 1.0 - F ) * attenuatedColor, 1.0 - ( 1.0 - transmittedLight.a ) * transmittanceFactor );
	}
#endif`,Ju=`#if defined( USE_UV ) || defined( USE_ANISOTROPY )
	varying vec2 vUv;
#endif
#ifdef USE_MAP
	varying vec2 vMapUv;
#endif
#ifdef USE_ALPHAMAP
	varying vec2 vAlphaMapUv;
#endif
#ifdef USE_LIGHTMAP
	varying vec2 vLightMapUv;
#endif
#ifdef USE_AOMAP
	varying vec2 vAoMapUv;
#endif
#ifdef USE_BUMPMAP
	varying vec2 vBumpMapUv;
#endif
#ifdef USE_NORMALMAP
	varying vec2 vNormalMapUv;
#endif
#ifdef USE_EMISSIVEMAP
	varying vec2 vEmissiveMapUv;
#endif
#ifdef USE_METALNESSMAP
	varying vec2 vMetalnessMapUv;
#endif
#ifdef USE_ROUGHNESSMAP
	varying vec2 vRoughnessMapUv;
#endif
#ifdef USE_DIFFUSE_ROUGHNESSMAP
	varying vec2 vDiffuseRoughnessMapUv;
#endif
#ifdef USE_ANISOTROPYMAP
	varying vec2 vAnisotropyMapUv;
#endif
#ifdef USE_CLEARCOATMAP
	varying vec2 vClearcoatMapUv;
#endif
#ifdef USE_CLEARCOAT_NORMALMAP
	varying vec2 vClearcoatNormalMapUv;
#endif
#ifdef USE_CLEARCOAT_ROUGHNESSMAP
	varying vec2 vClearcoatRoughnessMapUv;
#endif
#ifdef USE_IRIDESCENCEMAP
	varying vec2 vIridescenceMapUv;
#endif
#ifdef USE_IRIDESCENCE_THICKNESSMAP
	varying vec2 vIridescenceThicknessMapUv;
#endif
#ifdef USE_SHEEN_COLORMAP
	varying vec2 vSheenColorMapUv;
#endif
#ifdef USE_SHEEN_ROUGHNESSMAP
	varying vec2 vSheenRoughnessMapUv;
#endif
#ifdef USE_SPECULARMAP
	varying vec2 vSpecularMapUv;
#endif
#ifdef USE_SPECULAR_COLORMAP
	varying vec2 vSpecularColorMapUv;
#endif
#ifdef USE_SPECULAR_INTENSITYMAP
	varying vec2 vSpecularIntensityMapUv;
#endif
#ifdef USE_TRANSMISSIONMAP
	uniform mat3 transmissionMapTransform;
	varying vec2 vTransmissionMapUv;
#endif
#ifdef USE_THICKNESSMAP
	uniform mat3 thicknessMapTransform;
	varying vec2 vThicknessMapUv;
#endif`,Qu=`#if defined( USE_UV ) || defined( USE_ANISOTROPY )
	varying vec2 vUv;
#endif
#ifdef USE_MAP
	uniform mat3 mapTransform;
	varying vec2 vMapUv;
#endif
#ifdef USE_ALPHAMAP
	uniform mat3 alphaMapTransform;
	varying vec2 vAlphaMapUv;
#endif
#ifdef USE_LIGHTMAP
	uniform mat3 lightMapTransform;
	varying vec2 vLightMapUv;
#endif
#ifdef USE_AOMAP
	uniform mat3 aoMapTransform;
	varying vec2 vAoMapUv;
#endif
#ifdef USE_BUMPMAP
	uniform mat3 bumpMapTransform;
	varying vec2 vBumpMapUv;
#endif
#ifdef USE_NORMALMAP
	uniform mat3 normalMapTransform;
	varying vec2 vNormalMapUv;
#endif
#ifdef USE_DISPLACEMENTMAP
	uniform mat3 displacementMapTransform;
	varying vec2 vDisplacementMapUv;
#endif
#ifdef USE_EMISSIVEMAP
	uniform mat3 emissiveMapTransform;
	varying vec2 vEmissiveMapUv;
#endif
#ifdef USE_METALNESSMAP
	uniform mat3 metalnessMapTransform;
	varying vec2 vMetalnessMapUv;
#endif
#ifdef USE_ROUGHNESSMAP
	uniform mat3 roughnessMapTransform;
	varying vec2 vRoughnessMapUv;
#endif
#ifdef USE_DIFFUSE_ROUGHNESSMAP
	uniform mat3 diffuseRoughnessMapTransform;
	varying vec2 vDiffuseRoughnessMapUv;
#endif
#ifdef USE_ANISOTROPYMAP
	uniform mat3 anisotropyMapTransform;
	varying vec2 vAnisotropyMapUv;
#endif
#ifdef USE_CLEARCOATMAP
	uniform mat3 clearcoatMapTransform;
	varying vec2 vClearcoatMapUv;
#endif
#ifdef USE_CLEARCOAT_NORMALMAP
	uniform mat3 clearcoatNormalMapTransform;
	varying vec2 vClearcoatNormalMapUv;
#endif
#ifdef USE_CLEARCOAT_ROUGHNESSMAP
	uniform mat3 clearcoatRoughnessMapTransform;
	varying vec2 vClearcoatRoughnessMapUv;
#endif
#ifdef USE_SHEEN_COLORMAP
	uniform mat3 sheenColorMapTransform;
	varying vec2 vSheenColorMapUv;
#endif
#ifdef USE_SHEEN_ROUGHNESSMAP
	uniform mat3 sheenRoughnessMapTransform;
	varying vec2 vSheenRoughnessMapUv;
#endif
#ifdef USE_IRIDESCENCEMAP
	uniform mat3 iridescenceMapTransform;
	varying vec2 vIridescenceMapUv;
#endif
#ifdef USE_IRIDESCENCE_THICKNESSMAP
	uniform mat3 iridescenceThicknessMapTransform;
	varying vec2 vIridescenceThicknessMapUv;
#endif
#ifdef USE_SPECULARMAP
	uniform mat3 specularMapTransform;
	varying vec2 vSpecularMapUv;
#endif
#ifdef USE_SPECULAR_COLORMAP
	uniform mat3 specularColorMapTransform;
	varying vec2 vSpecularColorMapUv;
#endif
#ifdef USE_SPECULAR_INTENSITYMAP
	uniform mat3 specularIntensityMapTransform;
	varying vec2 vSpecularIntensityMapUv;
#endif
#ifdef USE_TRANSMISSIONMAP
	uniform mat3 transmissionMapTransform;
	varying vec2 vTransmissionMapUv;
#endif
#ifdef USE_THICKNESSMAP
	uniform mat3 thicknessMapTransform;
	varying vec2 vThicknessMapUv;
#endif`,ju=`#if defined( USE_UV ) || defined( USE_ANISOTROPY )
	vUv = vec3( uv, 1 ).xy;
#endif
#ifdef USE_MAP
	vMapUv = ( mapTransform * vec3( MAP_UV, 1 ) ).xy;
#endif
#ifdef USE_ALPHAMAP
	vAlphaMapUv = ( alphaMapTransform * vec3( ALPHAMAP_UV, 1 ) ).xy;
#endif
#ifdef USE_LIGHTMAP
	vLightMapUv = ( lightMapTransform * vec3( LIGHTMAP_UV, 1 ) ).xy;
#endif
#ifdef USE_AOMAP
	vAoMapUv = ( aoMapTransform * vec3( AOMAP_UV, 1 ) ).xy;
#endif
#ifdef USE_BUMPMAP
	vBumpMapUv = ( bumpMapTransform * vec3( BUMPMAP_UV, 1 ) ).xy;
#endif
#ifdef USE_NORMALMAP
	vNormalMapUv = ( normalMapTransform * vec3( NORMALMAP_UV, 1 ) ).xy;
#endif
#ifdef USE_DISPLACEMENTMAP
	vDisplacementMapUv = ( displacementMapTransform * vec3( DISPLACEMENTMAP_UV, 1 ) ).xy;
#endif
#ifdef USE_EMISSIVEMAP
	vEmissiveMapUv = ( emissiveMapTransform * vec3( EMISSIVEMAP_UV, 1 ) ).xy;
#endif
#ifdef USE_METALNESSMAP
	vMetalnessMapUv = ( metalnessMapTransform * vec3( METALNESSMAP_UV, 1 ) ).xy;
#endif
#ifdef USE_ROUGHNESSMAP
	vRoughnessMapUv = ( roughnessMapTransform * vec3( ROUGHNESSMAP_UV, 1 ) ).xy;
#endif
#ifdef USE_DIFFUSE_ROUGHNESSMAP
	vDiffuseRoughnessMapUv = ( diffuseRoughnessMapTransform * vec3( DIFFUSE_ROUGHNESSMAP_UV, 1 ) ).xy;
#endif
#ifdef USE_ANISOTROPYMAP
	vAnisotropyMapUv = ( anisotropyMapTransform * vec3( ANISOTROPYMAP_UV, 1 ) ).xy;
#endif
#ifdef USE_CLEARCOATMAP
	vClearcoatMapUv = ( clearcoatMapTransform * vec3( CLEARCOATMAP_UV, 1 ) ).xy;
#endif
#ifdef USE_CLEARCOAT_NORMALMAP
	vClearcoatNormalMapUv = ( clearcoatNormalMapTransform * vec3( CLEARCOAT_NORMALMAP_UV, 1 ) ).xy;
#endif
#ifdef USE_CLEARCOAT_ROUGHNESSMAP
	vClearcoatRoughnessMapUv = ( clearcoatRoughnessMapTransform * vec3( CLEARCOAT_ROUGHNESSMAP_UV, 1 ) ).xy;
#endif
#ifdef USE_IRIDESCENCEMAP
	vIridescenceMapUv = ( iridescenceMapTransform * vec3( IRIDESCENCEMAP_UV, 1 ) ).xy;
#endif
#ifdef USE_IRIDESCENCE_THICKNESSMAP
	vIridescenceThicknessMapUv = ( iridescenceThicknessMapTransform * vec3( IRIDESCENCE_THICKNESSMAP_UV, 1 ) ).xy;
#endif
#ifdef USE_SHEEN_COLORMAP
	vSheenColorMapUv = ( sheenColorMapTransform * vec3( SHEEN_COLORMAP_UV, 1 ) ).xy;
#endif
#ifdef USE_SHEEN_ROUGHNESSMAP
	vSheenRoughnessMapUv = ( sheenRoughnessMapTransform * vec3( SHEEN_ROUGHNESSMAP_UV, 1 ) ).xy;
#endif
#ifdef USE_SPECULARMAP
	vSpecularMapUv = ( specularMapTransform * vec3( SPECULARMAP_UV, 1 ) ).xy;
#endif
#ifdef USE_SPECULAR_COLORMAP
	vSpecularColorMapUv = ( specularColorMapTransform * vec3( SPECULAR_COLORMAP_UV, 1 ) ).xy;
#endif
#ifdef USE_SPECULAR_INTENSITYMAP
	vSpecularIntensityMapUv = ( specularIntensityMapTransform * vec3( SPECULAR_INTENSITYMAP_UV, 1 ) ).xy;
#endif
#ifdef USE_TRANSMISSIONMAP
	vTransmissionMapUv = ( transmissionMapTransform * vec3( TRANSMISSIONMAP_UV, 1 ) ).xy;
#endif
#ifdef USE_THICKNESSMAP
	vThicknessMapUv = ( thicknessMapTransform * vec3( THICKNESSMAP_UV, 1 ) ).xy;
#endif`,ef=`#if defined( USE_ENVMAP ) || defined( DISTANCE ) || defined ( USE_SHADOWMAP ) || defined ( USE_TRANSMISSION ) || NUM_SPOT_LIGHT_COORDS > 0
	vec4 worldPosition = vec4( transformed, 1.0 );
	#ifdef USE_BATCHING
		worldPosition = batchingMatrix * worldPosition;
	#endif
	#ifdef USE_INSTANCING
		worldPosition = instanceMatrix * worldPosition;
	#endif
	worldPosition = modelMatrix * worldPosition;
#endif`;const tf=`varying vec2 vUv;
uniform mat3 uvTransform;
void main() {
	vUv = ( uvTransform * vec3( uv, 1 ) ).xy;
	gl_Position = vec4( position.xy, 1.0, 1.0 );
}`,nf=`uniform sampler2D t2D;
uniform float backgroundIntensity;
varying vec2 vUv;
void main() {
	vec4 texColor = texture2D( t2D, vUv );
	#ifdef DECODE_VIDEO_TEXTURE
		texColor = vec4( mix( pow( texColor.rgb * 0.9478672986 + vec3( 0.0521327014 ), vec3( 2.4 ) ), texColor.rgb * 0.0773993808, vec3( lessThanEqual( texColor.rgb, vec3( 0.04045 ) ) ) ), texColor.w );
	#endif
	texColor.rgb *= backgroundIntensity;
	gl_FragColor = texColor;
	#include <tonemapping_fragment>
	#include <colorspace_fragment>
}`,sf=`varying vec3 vWorldDirection;
#include <common>
void main() {
	vWorldDirection = transformDirection( position, modelMatrix );
	#include <begin_vertex>
	#include <project_vertex>
	gl_Position.z = gl_Position.w;
}`,rf=`uniform float backgroundBlurriness;
uniform float backgroundIntensity;
uniform mat3 backgroundRotation;
varying vec3 vWorldDirection;
#include <envmap_common_pars_fragment>
void main() {
	#ifdef ENVMAP_TYPE_PMREM
		vec4 texColor = textureLod( envMap, backgroundRotation * vWorldDirection, roughnessToMip( backgroundBlurriness ) );
	#else
		vec4 texColor = textureCube( envMap, backgroundRotation * vWorldDirection );
	#endif
	texColor.rgb *= backgroundIntensity;
	gl_FragColor = texColor;
	#include <tonemapping_fragment>
	#include <colorspace_fragment>
}`,af=`varying vec3 vWorldDirection;
#include <common>
void main() {
	vWorldDirection = transformDirection( position, modelMatrix );
	#include <begin_vertex>
	#include <project_vertex>
	gl_Position.z = gl_Position.w;
}`,of=`uniform samplerCube tCube;
uniform float tFlip;
uniform float opacity;
varying vec3 vWorldDirection;
void main() {
	vec4 texColor = textureCube( tCube, vec3( tFlip * vWorldDirection.x, vWorldDirection.yz ) );
	gl_FragColor = texColor;
	gl_FragColor.a *= opacity;
	#include <tonemapping_fragment>
	#include <colorspace_fragment>
}`,lf=`#include <common>
#include <batching_pars_vertex>
#include <uv_pars_vertex>
#include <displacementmap_pars_vertex>
#include <morphtarget_pars_vertex>
#include <skinning_pars_vertex>
#include <logdepthbuf_pars_vertex>
#include <clipping_planes_pars_vertex>
varying vec2 vHighPrecisionZW;
void main() {
	#include <uv_vertex>
	#include <batching_vertex>
	#include <skinbase_vertex>
	#include <morphinstance_vertex>
	#ifdef USE_DISPLACEMENTMAP
		#include <beginnormal_vertex>
		#include <morphnormal_vertex>
		#include <skinnormal_vertex>
	#endif
	#include <begin_vertex>
	#include <morphtarget_vertex>
	#include <skinning_vertex>
	#include <displacementmap_vertex>
	#include <project_vertex>
	#include <logdepthbuf_vertex>
	#include <clipping_planes_vertex>
	vHighPrecisionZW = gl_Position.zw;
}`,cf=`#if DEPTH_PACKING == 3200
	uniform float opacity;
#endif
#include <common>
#include <packing>
#include <uv_pars_fragment>
#include <map_pars_fragment>
#include <alphamap_pars_fragment>
#include <alphatest_pars_fragment>
#include <alphahash_pars_fragment>
#include <logdepthbuf_pars_fragment>
#include <clipping_planes_pars_fragment>
varying vec2 vHighPrecisionZW;
void main() {
	vec4 diffuseColor = vec4( 1.0 );
	#include <clipping_planes_fragment>
	#if DEPTH_PACKING == 3200
		diffuseColor.a = opacity;
	#endif
	#include <map_fragment>
	#include <alphamap_fragment>
	#include <alphatest_fragment>
	#include <alphahash_fragment>
	#include <logdepthbuf_fragment>
	#ifdef USE_REVERSED_DEPTH_BUFFER
		float fragCoordZ = vHighPrecisionZW[ 0 ] / vHighPrecisionZW[ 1 ];
	#else
		float fragCoordZ = 0.5 * vHighPrecisionZW[ 0 ] / vHighPrecisionZW[ 1 ] + 0.5;
	#endif
	#if DEPTH_PACKING == 3200
		gl_FragColor = vec4( vec3( 1.0 - fragCoordZ ), opacity );
	#elif DEPTH_PACKING == 3201
		gl_FragColor = packDepthToRGBA( fragCoordZ );
	#elif DEPTH_PACKING == 3202
		gl_FragColor = vec4( packDepthToRGB( fragCoordZ ), 1.0 );
	#elif DEPTH_PACKING == 3203
		gl_FragColor = vec4( packDepthToRG( fragCoordZ ), 0.0, 1.0 );
	#endif
}`,df=`#define DISTANCE
varying vec3 vWorldPosition;
#include <common>
#include <batching_pars_vertex>
#include <uv_pars_vertex>
#include <displacementmap_pars_vertex>
#include <morphtarget_pars_vertex>
#include <skinning_pars_vertex>
#include <clipping_planes_pars_vertex>
void main() {
	#include <uv_vertex>
	#include <batching_vertex>
	#include <skinbase_vertex>
	#include <morphinstance_vertex>
	#ifdef USE_DISPLACEMENTMAP
		#include <beginnormal_vertex>
		#include <morphnormal_vertex>
		#include <skinnormal_vertex>
	#endif
	#include <begin_vertex>
	#include <morphtarget_vertex>
	#include <skinning_vertex>
	#include <displacementmap_vertex>
	#include <project_vertex>
	#include <worldpos_vertex>
	#include <clipping_planes_vertex>
	vWorldPosition = worldPosition.xyz;
}`,uf=`#define DISTANCE
uniform vec3 referencePosition;
uniform float nearDistance;
uniform float farDistance;
varying vec3 vWorldPosition;
#include <common>
#include <uv_pars_fragment>
#include <map_pars_fragment>
#include <alphamap_pars_fragment>
#include <alphatest_pars_fragment>
#include <alphahash_pars_fragment>
#include <clipping_planes_pars_fragment>
void main() {
	vec4 diffuseColor = vec4( 1.0 );
	#include <clipping_planes_fragment>
	#include <map_fragment>
	#include <alphamap_fragment>
	#include <alphatest_fragment>
	#include <alphahash_fragment>
	float dist = length( vWorldPosition - referencePosition );
	dist = ( dist - nearDistance ) / ( farDistance - nearDistance );
	dist = saturate( dist );
	gl_FragColor = vec4( dist, 0.0, 0.0, 1.0 );
}`,ff=`varying vec3 vWorldDirection;
#include <common>
void main() {
	vWorldDirection = transformDirection( position, modelMatrix );
	#include <begin_vertex>
	#include <project_vertex>
}`,hf=`uniform sampler2D tEquirect;
varying vec3 vWorldDirection;
#include <common>
void main() {
	vec3 direction = normalize( vWorldDirection );
	vec2 sampleUV = equirectUv( direction );
	gl_FragColor = texture2D( tEquirect, sampleUV );
	#include <tonemapping_fragment>
	#include <colorspace_fragment>
}`,pf=`uniform float scale;
attribute float lineDistance;
varying float vLineDistance;
#include <common>
#include <uv_pars_vertex>
#include <color_pars_vertex>
#include <fog_pars_vertex>
#include <morphtarget_pars_vertex>
#include <logdepthbuf_pars_vertex>
#include <clipping_planes_pars_vertex>
void main() {
	vLineDistance = scale * lineDistance;
	#include <uv_vertex>
	#include <color_vertex>
	#include <morphinstance_vertex>
	#include <morphcolor_vertex>
	#include <begin_vertex>
	#include <morphtarget_vertex>
	#include <project_vertex>
	#include <logdepthbuf_vertex>
	#include <clipping_planes_vertex>
	#include <fog_vertex>
}`,mf=`uniform vec3 diffuse;
uniform float opacity;
uniform float dashSize;
uniform float totalSize;
varying float vLineDistance;
#include <common>
#include <color_pars_fragment>
#include <uv_pars_fragment>
#include <map_pars_fragment>
#include <fog_pars_fragment>
#include <logdepthbuf_pars_fragment>
#include <clipping_planes_pars_fragment>
void main() {
	vec4 diffuseColor = vec4( diffuse, opacity );
	#include <clipping_planes_fragment>
	if ( mod( vLineDistance, totalSize ) > dashSize ) {
		discard;
	}
	vec3 outgoingLight = vec3( 0.0 );
	#include <logdepthbuf_fragment>
	#include <map_fragment>
	#include <color_fragment>
	outgoingLight = diffuseColor.rgb;
	#include <opaque_fragment>
	#include <tonemapping_fragment>
	#include <colorspace_fragment>
	#include <fog_fragment>
	#include <premultiplied_alpha_fragment>
}`,gf=`#include <common>
#include <batching_pars_vertex>
#include <uv_pars_vertex>
#include <envmap_pars_vertex>
#include <color_pars_vertex>
#include <fog_pars_vertex>
#include <morphtarget_pars_vertex>
#include <skinning_pars_vertex>
#include <logdepthbuf_pars_vertex>
#include <clipping_planes_pars_vertex>
void main() {
	#include <uv_vertex>
	#include <color_vertex>
	#include <morphinstance_vertex>
	#include <morphcolor_vertex>
	#include <batching_vertex>
	#if defined ( USE_ENVMAP ) || defined ( USE_SKINNING )
		#include <beginnormal_vertex>
		#include <morphnormal_vertex>
		#include <skinbase_vertex>
		#include <skinnormal_vertex>
		#include <defaultnormal_vertex>
	#endif
	#include <begin_vertex>
	#include <morphtarget_vertex>
	#include <skinning_vertex>
	#include <project_vertex>
	#include <logdepthbuf_vertex>
	#include <clipping_planes_vertex>
	#include <worldpos_vertex>
	#include <envmap_vertex>
	#include <fog_vertex>
}`,xf=`uniform vec3 diffuse;
uniform float opacity;
#ifndef FLAT_SHADED
	varying vec3 vNormal;
#endif
#include <common>
#include <dithering_pars_fragment>
#include <color_pars_fragment>
#include <uv_pars_fragment>
#include <map_pars_fragment>
#include <alphamap_pars_fragment>
#include <alphatest_pars_fragment>
#include <alphahash_pars_fragment>
#include <aomap_pars_fragment>
#include <lightmap_pars_fragment>
#include <envmap_common_pars_fragment>
#include <envmap_pars_fragment>
#include <fog_pars_fragment>
#include <specularmap_pars_fragment>
#include <logdepthbuf_pars_fragment>
#include <clipping_planes_pars_fragment>
void main() {
	vec4 diffuseColor = vec4( diffuse, opacity );
	#include <clipping_planes_fragment>
	#include <logdepthbuf_fragment>
	#include <map_fragment>
	#include <color_fragment>
	#include <alphamap_fragment>
	#include <alphatest_fragment>
	#include <alphahash_fragment>
	#include <specularmap_fragment>
	ReflectedLight reflectedLight = ReflectedLight( vec3( 0.0 ), vec3( 0.0 ), vec3( 0.0 ), vec3( 0.0 ) );
	#ifdef USE_LIGHTMAP
		vec4 lightMapTexel = texture2D( lightMap, vLightMapUv );
		reflectedLight.indirectDiffuse += lightMapTexel.rgb * lightMapIntensity * RECIPROCAL_PI;
	#else
		reflectedLight.indirectDiffuse += vec3( 1.0 );
	#endif
	#include <aomap_fragment>
	reflectedLight.indirectDiffuse *= diffuseColor.rgb;
	vec3 outgoingLight = reflectedLight.indirectDiffuse;
	#include <envmap_fragment>
	#include <opaque_fragment>
	#include <tonemapping_fragment>
	#include <colorspace_fragment>
	#include <fog_fragment>
	#include <premultiplied_alpha_fragment>
	#include <dithering_fragment>
}`,_f=`#define LAMBERT
varying vec3 vViewPosition;
#include <common>
#include <batching_pars_vertex>
#include <uv_pars_vertex>
#include <displacementmap_pars_vertex>
#include <envmap_pars_vertex>
#include <color_pars_vertex>
#include <fog_pars_vertex>
#include <normal_pars_vertex>
#include <morphtarget_pars_vertex>
#include <skinning_pars_vertex>
#include <shadowmap_pars_vertex>
#include <logdepthbuf_pars_vertex>
#include <clipping_planes_pars_vertex>
void main() {
	#include <uv_vertex>
	#include <color_vertex>
	#include <morphinstance_vertex>
	#include <morphcolor_vertex>
	#include <batching_vertex>
	#include <beginnormal_vertex>
	#include <morphnormal_vertex>
	#include <skinbase_vertex>
	#include <skinnormal_vertex>
	#include <defaultnormal_vertex>
	#include <normal_vertex>
	#include <begin_vertex>
	#include <morphtarget_vertex>
	#include <skinning_vertex>
	#include <displacementmap_vertex>
	#include <project_vertex>
	#include <logdepthbuf_vertex>
	#include <clipping_planes_vertex>
	vViewPosition = - mvPosition.xyz;
	#include <worldpos_vertex>
	#include <envmap_vertex>
	#include <shadowmap_vertex>
	#include <fog_vertex>
}`,vf=`#define LAMBERT
uniform vec3 diffuse;
uniform vec3 emissive;
uniform float opacity;
#include <common>
#include <dithering_pars_fragment>
#include <color_pars_fragment>
#include <uv_pars_fragment>
#include <map_pars_fragment>
#include <alphamap_pars_fragment>
#include <alphatest_pars_fragment>
#include <alphahash_pars_fragment>
#include <aomap_pars_fragment>
#include <lightmap_pars_fragment>
#include <emissivemap_pars_fragment>
#include <envmap_common_pars_fragment>
#include <envmap_pars_fragment>
#include <envmap_physical_pars_fragment>
#include <fog_pars_fragment>
#include <bsdfs>
#include <lights_pars_begin>
#include <normal_pars_fragment>
#include <lights_lambert_pars_fragment>
#include <shadowmap_pars_fragment>
#include <bumpmap_pars_fragment>
#include <normalmap_pars_fragment>
#include <specularmap_pars_fragment>
#include <logdepthbuf_pars_fragment>
#include <clipping_planes_pars_fragment>
void main() {
	vec4 diffuseColor = vec4( diffuse, opacity );
	#include <clipping_planes_fragment>
	ReflectedLight reflectedLight = ReflectedLight( vec3( 0.0 ), vec3( 0.0 ), vec3( 0.0 ), vec3( 0.0 ) );
	vec3 totalEmissiveRadiance = emissive;
	#include <logdepthbuf_fragment>
	#include <map_fragment>
	#include <color_fragment>
	#include <alphamap_fragment>
	#include <alphatest_fragment>
	#include <alphahash_fragment>
	#include <specularmap_fragment>
	#include <normal_fragment_begin>
	#include <normal_fragment_maps>
	#include <emissivemap_fragment>
	#include <lights_lambert_fragment>
	#include <lights_fragment_begin>
	#include <lights_fragment_maps>
	#include <lights_fragment_end>
	#include <aomap_fragment>
	vec3 outgoingLight = reflectedLight.directDiffuse + reflectedLight.indirectDiffuse + totalEmissiveRadiance;
	#include <envmap_fragment>
	#include <opaque_fragment>
	#include <tonemapping_fragment>
	#include <colorspace_fragment>
	#include <fog_fragment>
	#include <premultiplied_alpha_fragment>
	#include <dithering_fragment>
}`,Sf=`#define MATCAP
varying vec3 vViewPosition;
#include <common>
#include <batching_pars_vertex>
#include <uv_pars_vertex>
#include <color_pars_vertex>
#include <displacementmap_pars_vertex>
#include <fog_pars_vertex>
#include <normal_pars_vertex>
#include <morphtarget_pars_vertex>
#include <skinning_pars_vertex>
#include <logdepthbuf_pars_vertex>
#include <clipping_planes_pars_vertex>
void main() {
	#include <uv_vertex>
	#include <color_vertex>
	#include <morphinstance_vertex>
	#include <morphcolor_vertex>
	#include <batching_vertex>
	#include <beginnormal_vertex>
	#include <morphnormal_vertex>
	#include <skinbase_vertex>
	#include <skinnormal_vertex>
	#include <defaultnormal_vertex>
	#include <normal_vertex>
	#include <begin_vertex>
	#include <morphtarget_vertex>
	#include <skinning_vertex>
	#include <displacementmap_vertex>
	#include <project_vertex>
	#include <logdepthbuf_vertex>
	#include <clipping_planes_vertex>
	#include <fog_vertex>
	vViewPosition = - mvPosition.xyz;
}`,Mf=`#define MATCAP
uniform vec3 diffuse;
uniform float opacity;
uniform sampler2D matcap;
varying vec3 vViewPosition;
#include <common>
#include <dithering_pars_fragment>
#include <color_pars_fragment>
#include <uv_pars_fragment>
#include <map_pars_fragment>
#include <alphamap_pars_fragment>
#include <alphatest_pars_fragment>
#include <alphahash_pars_fragment>
#include <fog_pars_fragment>
#include <normal_pars_fragment>
#include <bumpmap_pars_fragment>
#include <normalmap_pars_fragment>
#include <logdepthbuf_pars_fragment>
#include <clipping_planes_pars_fragment>
void main() {
	vec4 diffuseColor = vec4( diffuse, opacity );
	#include <clipping_planes_fragment>
	#include <logdepthbuf_fragment>
	#include <map_fragment>
	#include <color_fragment>
	#include <alphamap_fragment>
	#include <alphatest_fragment>
	#include <alphahash_fragment>
	#include <normal_fragment_begin>
	#include <normal_fragment_maps>
	vec3 viewDir = normalize( vViewPosition );
	vec3 x = normalize( vec3( viewDir.z, 0.0, - viewDir.x ) );
	vec3 y = cross( viewDir, x );
	vec2 uv = vec2( dot( x, normal ), dot( y, normal ) ) * 0.495 + 0.5;
	#ifdef USE_MATCAP
		vec4 matcapColor = texture2D( matcap, uv );
	#else
		vec4 matcapColor = vec4( vec3( mix( 0.2, 0.8, uv.y ) ), 1.0 );
	#endif
	vec3 outgoingLight = diffuseColor.rgb * matcapColor.rgb;
	#include <opaque_fragment>
	#include <tonemapping_fragment>
	#include <colorspace_fragment>
	#include <fog_fragment>
	#include <premultiplied_alpha_fragment>
	#include <dithering_fragment>
}`,bf=`#define NORMAL
#if defined( FLAT_SHADED ) || defined( USE_BUMPMAP ) || defined( USE_NORMALMAP_TANGENTSPACE )
	varying vec3 vViewPosition;
#endif
#include <common>
#include <batching_pars_vertex>
#include <uv_pars_vertex>
#include <displacementmap_pars_vertex>
#include <normal_pars_vertex>
#include <morphtarget_pars_vertex>
#include <skinning_pars_vertex>
#include <logdepthbuf_pars_vertex>
#include <clipping_planes_pars_vertex>
void main() {
	#include <uv_vertex>
	#include <batching_vertex>
	#include <beginnormal_vertex>
	#include <morphinstance_vertex>
	#include <morphnormal_vertex>
	#include <skinbase_vertex>
	#include <skinnormal_vertex>
	#include <defaultnormal_vertex>
	#include <normal_vertex>
	#include <begin_vertex>
	#include <morphtarget_vertex>
	#include <skinning_vertex>
	#include <displacementmap_vertex>
	#include <project_vertex>
	#include <logdepthbuf_vertex>
	#include <clipping_planes_vertex>
#if defined( FLAT_SHADED ) || defined( USE_BUMPMAP ) || defined( USE_NORMALMAP_TANGENTSPACE )
	vViewPosition = - mvPosition.xyz;
#endif
}`,yf=`#define NORMAL
uniform float opacity;
#if defined( FLAT_SHADED ) || defined( USE_BUMPMAP ) || defined( USE_NORMALMAP_TANGENTSPACE )
	varying vec3 vViewPosition;
#endif
#include <uv_pars_fragment>
#include <normal_pars_fragment>
#include <bumpmap_pars_fragment>
#include <normalmap_pars_fragment>
#include <logdepthbuf_pars_fragment>
#include <clipping_planes_pars_fragment>
void main() {
	vec4 diffuseColor = vec4( 0.0, 0.0, 0.0, opacity );
	#include <clipping_planes_fragment>
	#include <logdepthbuf_fragment>
	#include <normal_fragment_begin>
	#include <normal_fragment_maps>
	gl_FragColor = vec4( normalize( normal ) * 0.5 + 0.5, diffuseColor.a );
	#ifdef OPAQUE
		gl_FragColor.a = 1.0;
	#endif
}`,Ef=`#define PHONG
varying vec3 vViewPosition;
#include <common>
#include <batching_pars_vertex>
#include <uv_pars_vertex>
#include <displacementmap_pars_vertex>
#include <envmap_pars_vertex>
#include <color_pars_vertex>
#include <fog_pars_vertex>
#include <normal_pars_vertex>
#include <morphtarget_pars_vertex>
#include <skinning_pars_vertex>
#include <shadowmap_pars_vertex>
#include <logdepthbuf_pars_vertex>
#include <clipping_planes_pars_vertex>
void main() {
	#include <uv_vertex>
	#include <color_vertex>
	#include <morphcolor_vertex>
	#include <batching_vertex>
	#include <beginnormal_vertex>
	#include <morphinstance_vertex>
	#include <morphnormal_vertex>
	#include <skinbase_vertex>
	#include <skinnormal_vertex>
	#include <defaultnormal_vertex>
	#include <normal_vertex>
	#include <begin_vertex>
	#include <morphtarget_vertex>
	#include <skinning_vertex>
	#include <displacementmap_vertex>
	#include <project_vertex>
	#include <logdepthbuf_vertex>
	#include <clipping_planes_vertex>
	vViewPosition = - mvPosition.xyz;
	#include <worldpos_vertex>
	#include <envmap_vertex>
	#include <shadowmap_vertex>
	#include <fog_vertex>
}`,Tf=`#define PHONG
uniform vec3 diffuse;
uniform vec3 emissive;
uniform vec3 specular;
uniform float shininess;
uniform float opacity;
#include <common>
#include <dithering_pars_fragment>
#include <color_pars_fragment>
#include <uv_pars_fragment>
#include <map_pars_fragment>
#include <alphamap_pars_fragment>
#include <alphatest_pars_fragment>
#include <alphahash_pars_fragment>
#include <aomap_pars_fragment>
#include <lightmap_pars_fragment>
#include <emissivemap_pars_fragment>
#include <envmap_common_pars_fragment>
#include <envmap_pars_fragment>
#include <envmap_physical_pars_fragment>
#include <fog_pars_fragment>
#include <bsdfs>
#include <lights_pars_begin>
#include <normal_pars_fragment>
#include <lights_phong_pars_fragment>
#include <shadowmap_pars_fragment>
#include <bumpmap_pars_fragment>
#include <normalmap_pars_fragment>
#include <specularmap_pars_fragment>
#include <logdepthbuf_pars_fragment>
#include <clipping_planes_pars_fragment>
void main() {
	vec4 diffuseColor = vec4( diffuse, opacity );
	#include <clipping_planes_fragment>
	ReflectedLight reflectedLight = ReflectedLight( vec3( 0.0 ), vec3( 0.0 ), vec3( 0.0 ), vec3( 0.0 ) );
	vec3 totalEmissiveRadiance = emissive;
	#include <logdepthbuf_fragment>
	#include <map_fragment>
	#include <color_fragment>
	#include <alphamap_fragment>
	#include <alphatest_fragment>
	#include <alphahash_fragment>
	#include <specularmap_fragment>
	#include <normal_fragment_begin>
	#include <normal_fragment_maps>
	#include <emissivemap_fragment>
	#include <lights_phong_fragment>
	#include <lights_fragment_begin>
	#include <lights_fragment_maps>
	#include <lights_fragment_end>
	#include <aomap_fragment>
	vec3 outgoingLight = reflectedLight.directDiffuse + reflectedLight.indirectDiffuse + reflectedLight.directSpecular + reflectedLight.indirectSpecular + totalEmissiveRadiance;
	#include <envmap_fragment>
	#include <opaque_fragment>
	#include <tonemapping_fragment>
	#include <colorspace_fragment>
	#include <fog_fragment>
	#include <premultiplied_alpha_fragment>
	#include <dithering_fragment>
}`,wf=`#define STANDARD
varying vec3 vViewPosition;
#ifdef USE_TRANSMISSION
	varying vec3 vWorldPosition;
#endif
#include <common>
#include <batching_pars_vertex>
#include <uv_pars_vertex>
#include <displacementmap_pars_vertex>
#include <color_pars_vertex>
#include <fog_pars_vertex>
#include <normal_pars_vertex>
#include <morphtarget_pars_vertex>
#include <skinning_pars_vertex>
#include <shadowmap_pars_vertex>
#include <logdepthbuf_pars_vertex>
#include <clipping_planes_pars_vertex>
void main() {
	#include <uv_vertex>
	#include <color_vertex>
	#include <morphinstance_vertex>
	#include <morphcolor_vertex>
	#include <batching_vertex>
	#include <beginnormal_vertex>
	#include <morphnormal_vertex>
	#include <skinbase_vertex>
	#include <skinnormal_vertex>
	#include <defaultnormal_vertex>
	#include <normal_vertex>
	#include <begin_vertex>
	#include <morphtarget_vertex>
	#include <skinning_vertex>
	#include <displacementmap_vertex>
	#include <project_vertex>
	#include <logdepthbuf_vertex>
	#include <clipping_planes_vertex>
	vViewPosition = - mvPosition.xyz;
	#include <worldpos_vertex>
	#include <shadowmap_vertex>
	#include <fog_vertex>
#ifdef USE_TRANSMISSION
	vWorldPosition = worldPosition.xyz;
#endif
}`,Af=`#define STANDARD
#ifdef PHYSICAL
	#define IOR
	#define USE_SPECULAR
#endif
uniform vec3 diffuse;
uniform vec3 emissive;
uniform float roughness;
uniform float metalness;
uniform float opacity;
#ifdef IOR
	uniform float ior;
#endif
#ifdef USE_SPECULAR
	uniform float specularIntensity;
	uniform vec3 specularColor;
	#ifdef USE_SPECULAR_COLORMAP
		uniform sampler2D specularColorMap;
	#endif
	#ifdef USE_SPECULAR_INTENSITYMAP
		uniform sampler2D specularIntensityMap;
	#endif
#endif
#ifdef USE_CLEARCOAT
	uniform float clearcoat;
	uniform float clearcoatRoughness;
#endif
#ifdef USE_DIFFUSE_ROUGHNESS
	uniform float diffuseRoughness;
	#ifdef USE_DIFFUSE_ROUGHNESSMAP
		uniform sampler2D diffuseRoughnessMap;
	#endif
#endif
#ifdef USE_DISPERSION
	uniform float dispersion;
#endif
#ifdef USE_RETROREFLECTION
	uniform float retroreflectivity;
#endif
#ifdef USE_IRIDESCENCE
	uniform float iridescence;
	uniform float iridescenceIOR;
	uniform float iridescenceThicknessMinimum;
	uniform float iridescenceThicknessMaximum;
#endif
#ifdef USE_SHEEN
	uniform vec3 sheenColor;
	uniform float sheenRoughness;
	#ifdef USE_SHEEN_COLORMAP
		uniform sampler2D sheenColorMap;
	#endif
	#ifdef USE_SHEEN_ROUGHNESSMAP
		uniform sampler2D sheenRoughnessMap;
	#endif
#endif
#ifdef USE_ANISOTROPY
	uniform vec2 anisotropyVector;
	#ifdef USE_ANISOTROPYMAP
		uniform sampler2D anisotropyMap;
	#endif
#endif
varying vec3 vViewPosition;
#include <common>
#include <dithering_pars_fragment>
#include <color_pars_fragment>
#include <uv_pars_fragment>
#include <map_pars_fragment>
#include <alphamap_pars_fragment>
#include <alphatest_pars_fragment>
#include <alphahash_pars_fragment>
#include <aomap_pars_fragment>
#include <lightmap_pars_fragment>
#include <emissivemap_pars_fragment>
#include <iridescence_fragment>
#include <envmap_common_pars_fragment>
#include <envmap_physical_pars_fragment>
#include <fog_pars_fragment>
#include <lights_pars_begin>
#include <normal_pars_fragment>
#include <lights_physical_pars_fragment>
#include <transmission_pars_fragment>
#include <shadowmap_pars_fragment>
#include <bumpmap_pars_fragment>
#include <normalmap_pars_fragment>
#include <clearcoat_pars_fragment>
#include <iridescence_pars_fragment>
#include <roughnessmap_pars_fragment>
#include <metalnessmap_pars_fragment>
#include <logdepthbuf_pars_fragment>
#include <clipping_planes_pars_fragment>
void main() {
	vec4 diffuseColor = vec4( diffuse, opacity );
	#include <clipping_planes_fragment>
	ReflectedLight reflectedLight = ReflectedLight( vec3( 0.0 ), vec3( 0.0 ), vec3( 0.0 ), vec3( 0.0 ) );
	vec3 totalEmissiveRadiance = emissive;
	#include <logdepthbuf_fragment>
	#include <map_fragment>
	#include <color_fragment>
	#include <alphamap_fragment>
	#include <alphatest_fragment>
	#include <alphahash_fragment>
	#include <roughnessmap_fragment>
	#include <metalnessmap_fragment>
	#include <normal_fragment_begin>
	#include <normal_fragment_maps>
	#include <clearcoat_normal_fragment_begin>
	#include <clearcoat_normal_fragment_maps>
	#include <emissivemap_fragment>
	#include <lights_physical_fragment>
	#include <lights_fragment_begin>
	#include <lights_fragment_maps>
	#include <lights_fragment_end>
	#include <aomap_fragment>
	vec3 totalDiffuse = reflectedLight.directDiffuse + reflectedLight.indirectDiffuse;
	vec3 totalSpecular = reflectedLight.directSpecular + reflectedLight.indirectSpecular;
	#include <transmission_fragment>
	vec3 outgoingLight = totalDiffuse + totalSpecular + totalEmissiveRadiance;
	#ifdef USE_SHEEN
 
		outgoingLight = outgoingLight + sheenSpecularDirect + sheenSpecularIndirect;
 
 	#endif
	#ifdef USE_CLEARCOAT
		float dotNVcc = saturate( dot( geometryClearcoatNormal, geometryViewDir ) );
		vec3 Fcc = F_Schlick( material.clearcoatF0, material.clearcoatF90, dotNVcc );
		outgoingLight = outgoingLight * ( 1.0 - material.clearcoat * Fcc ) + ( clearcoatSpecularDirect + clearcoatSpecularIndirect ) * material.clearcoat;
	#endif
	#include <opaque_fragment>
	#include <tonemapping_fragment>
	#include <colorspace_fragment>
	#include <fog_fragment>
	#include <premultiplied_alpha_fragment>
	#include <dithering_fragment>
}`,Rf=`#define TOON
varying vec3 vViewPosition;
#include <common>
#include <batching_pars_vertex>
#include <uv_pars_vertex>
#include <displacementmap_pars_vertex>
#include <color_pars_vertex>
#include <fog_pars_vertex>
#include <normal_pars_vertex>
#include <morphtarget_pars_vertex>
#include <skinning_pars_vertex>
#include <shadowmap_pars_vertex>
#include <logdepthbuf_pars_vertex>
#include <clipping_planes_pars_vertex>
void main() {
	#include <uv_vertex>
	#include <color_vertex>
	#include <morphinstance_vertex>
	#include <morphcolor_vertex>
	#include <batching_vertex>
	#include <beginnormal_vertex>
	#include <morphnormal_vertex>
	#include <skinbase_vertex>
	#include <skinnormal_vertex>
	#include <defaultnormal_vertex>
	#include <normal_vertex>
	#include <begin_vertex>
	#include <morphtarget_vertex>
	#include <skinning_vertex>
	#include <displacementmap_vertex>
	#include <project_vertex>
	#include <logdepthbuf_vertex>
	#include <clipping_planes_vertex>
	vViewPosition = - mvPosition.xyz;
	#include <worldpos_vertex>
	#include <shadowmap_vertex>
	#include <fog_vertex>
}`,Cf=`#define TOON
uniform vec3 diffuse;
uniform vec3 emissive;
uniform float opacity;
#include <common>
#include <dithering_pars_fragment>
#include <color_pars_fragment>
#include <uv_pars_fragment>
#include <map_pars_fragment>
#include <alphamap_pars_fragment>
#include <alphatest_pars_fragment>
#include <alphahash_pars_fragment>
#include <aomap_pars_fragment>
#include <lightmap_pars_fragment>
#include <emissivemap_pars_fragment>
#include <gradientmap_pars_fragment>
#include <fog_pars_fragment>
#include <bsdfs>
#include <lights_pars_begin>
#include <normal_pars_fragment>
#include <lights_toon_pars_fragment>
#include <shadowmap_pars_fragment>
#include <bumpmap_pars_fragment>
#include <normalmap_pars_fragment>
#include <logdepthbuf_pars_fragment>
#include <clipping_planes_pars_fragment>
void main() {
	vec4 diffuseColor = vec4( diffuse, opacity );
	#include <clipping_planes_fragment>
	ReflectedLight reflectedLight = ReflectedLight( vec3( 0.0 ), vec3( 0.0 ), vec3( 0.0 ), vec3( 0.0 ) );
	vec3 totalEmissiveRadiance = emissive;
	#include <logdepthbuf_fragment>
	#include <map_fragment>
	#include <color_fragment>
	#include <alphamap_fragment>
	#include <alphatest_fragment>
	#include <alphahash_fragment>
	#include <normal_fragment_begin>
	#include <normal_fragment_maps>
	#include <emissivemap_fragment>
	#include <lights_toon_fragment>
	#include <lights_fragment_begin>
	#include <lights_fragment_maps>
	#include <lights_fragment_end>
	#include <aomap_fragment>
	vec3 outgoingLight = reflectedLight.directDiffuse + reflectedLight.indirectDiffuse + totalEmissiveRadiance;
	#include <opaque_fragment>
	#include <tonemapping_fragment>
	#include <colorspace_fragment>
	#include <fog_fragment>
	#include <premultiplied_alpha_fragment>
	#include <dithering_fragment>
}`,Pf=`uniform float size;
uniform float scale;
#include <common>
#include <color_pars_vertex>
#include <fog_pars_vertex>
#include <morphtarget_pars_vertex>
#include <logdepthbuf_pars_vertex>
#include <clipping_planes_pars_vertex>
#ifdef USE_POINTS_UV
	varying vec2 vUv;
	uniform mat3 uvTransform;
#endif
void main() {
	#ifdef USE_POINTS_UV
		vUv = ( uvTransform * vec3( uv, 1 ) ).xy;
	#endif
	#include <color_vertex>
	#include <morphinstance_vertex>
	#include <morphcolor_vertex>
	#include <begin_vertex>
	#include <morphtarget_vertex>
	#include <project_vertex>
	gl_PointSize = size;
	#ifdef USE_SIZEATTENUATION
		bool isPerspective = isPerspectiveMatrix( projectionMatrix );
		if ( isPerspective ) gl_PointSize *= ( scale / - mvPosition.z );
	#endif
	#include <logdepthbuf_vertex>
	#include <clipping_planes_vertex>
	#include <worldpos_vertex>
	#include <fog_vertex>
}`,Lf=`uniform vec3 diffuse;
uniform float opacity;
#include <common>
#include <color_pars_fragment>
#include <map_particle_pars_fragment>
#include <alphatest_pars_fragment>
#include <alphahash_pars_fragment>
#include <fog_pars_fragment>
#include <logdepthbuf_pars_fragment>
#include <clipping_planes_pars_fragment>
void main() {
	vec4 diffuseColor = vec4( diffuse, opacity );
	#include <clipping_planes_fragment>
	vec3 outgoingLight = vec3( 0.0 );
	#include <logdepthbuf_fragment>
	#include <map_particle_fragment>
	#include <color_fragment>
	#include <alphatest_fragment>
	#include <alphahash_fragment>
	outgoingLight = diffuseColor.rgb;
	#include <opaque_fragment>
	#include <tonemapping_fragment>
	#include <colorspace_fragment>
	#include <fog_fragment>
	#include <premultiplied_alpha_fragment>
}`,Df=`#include <common>
#include <batching_pars_vertex>
#include <fog_pars_vertex>
#include <morphtarget_pars_vertex>
#include <skinning_pars_vertex>
#include <logdepthbuf_pars_vertex>
#include <shadowmap_pars_vertex>
void main() {
	#include <batching_vertex>
	#include <beginnormal_vertex>
	#include <morphinstance_vertex>
	#include <morphnormal_vertex>
	#include <skinbase_vertex>
	#include <skinnormal_vertex>
	#include <defaultnormal_vertex>
	#include <begin_vertex>
	#include <morphtarget_vertex>
	#include <skinning_vertex>
	#include <project_vertex>
	#include <logdepthbuf_vertex>
	#include <worldpos_vertex>
	#include <shadowmap_vertex>
	#include <fog_vertex>
}`,If=`uniform vec3 color;
uniform float opacity;
#include <common>
#include <fog_pars_fragment>
#include <bsdfs>
#include <lights_pars_begin>
#include <logdepthbuf_pars_fragment>
#include <shadowmap_pars_fragment>
#include <shadowmask_pars_fragment>
void main() {
	#include <logdepthbuf_fragment>
	gl_FragColor = vec4( color, opacity * ( 1.0 - getShadowMask() ) );
	#include <tonemapping_fragment>
	#include <colorspace_fragment>
	#include <fog_fragment>
	#include <premultiplied_alpha_fragment>
}`,Nf=`uniform float rotation;
uniform vec2 center;
#include <common>
#include <uv_pars_vertex>
#include <fog_pars_vertex>
#include <logdepthbuf_pars_vertex>
#include <clipping_planes_pars_vertex>
void main() {
	#include <uv_vertex>
	vec4 mvPosition = modelViewMatrix[ 3 ];
	vec2 scale = vec2( length( modelMatrix[ 0 ].xyz ), length( modelMatrix[ 1 ].xyz ) );
	#ifndef USE_SIZEATTENUATION
		bool isPerspective = isPerspectiveMatrix( projectionMatrix );
		if ( isPerspective ) scale *= - mvPosition.z;
	#endif
	vec2 alignedPosition = ( position.xy - ( center - vec2( 0.5 ) ) ) * scale;
	vec2 rotatedPosition;
	rotatedPosition.x = cos( rotation ) * alignedPosition.x - sin( rotation ) * alignedPosition.y;
	rotatedPosition.y = sin( rotation ) * alignedPosition.x + cos( rotation ) * alignedPosition.y;
	mvPosition.xy += rotatedPosition;
	gl_Position = projectionMatrix * mvPosition;
	#include <logdepthbuf_vertex>
	#include <clipping_planes_vertex>
	#include <fog_vertex>
}`,Uf=`uniform vec3 diffuse;
uniform float opacity;
#include <common>
#include <uv_pars_fragment>
#include <map_pars_fragment>
#include <alphamap_pars_fragment>
#include <alphatest_pars_fragment>
#include <alphahash_pars_fragment>
#include <fog_pars_fragment>
#include <logdepthbuf_pars_fragment>
#include <clipping_planes_pars_fragment>
void main() {
	vec4 diffuseColor = vec4( diffuse, opacity );
	#include <clipping_planes_fragment>
	vec3 outgoingLight = vec3( 0.0 );
	#include <logdepthbuf_fragment>
	#include <map_fragment>
	#include <alphamap_fragment>
	#include <alphatest_fragment>
	#include <alphahash_fragment>
	outgoingLight = diffuseColor.rgb;
	#include <opaque_fragment>
	#include <tonemapping_fragment>
	#include <colorspace_fragment>
	#include <fog_fragment>
}`,Ve={alphahash_fragment:nd,alphahash_pars_fragment:id,alphamap_fragment:sd,alphamap_pars_fragment:rd,alphatest_fragment:ad,alphatest_pars_fragment:od,aomap_fragment:ld,aomap_pars_fragment:cd,batching_pars_vertex:dd,batching_vertex:ud,begin_vertex:fd,beginnormal_vertex:hd,bsdfs:pd,iridescence_fragment:md,bumpmap_pars_fragment:gd,clipping_planes_fragment:xd,clipping_planes_pars_fragment:_d,clipping_planes_pars_vertex:vd,clipping_planes_vertex:Sd,color_fragment:Md,color_pars_fragment:bd,color_pars_vertex:yd,color_vertex:Ed,common:Td,defaultnormal_vertex:wd,displacementmap_pars_vertex:Ad,displacementmap_vertex:Rd,emissivemap_fragment:Cd,emissivemap_pars_fragment:Pd,colorspace_fragment:Ld,colorspace_pars_fragment:Dd,envmap_fragment:Id,envmap_common_pars_fragment:Nd,envmap_pars_fragment:Ud,envmap_pars_vertex:Fd,envmap_physical_pars_fragment:Yd,envmap_vertex:Od,fog_vertex:Bd,fog_pars_vertex:kd,fog_fragment:zd,fog_pars_fragment:Gd,gradientmap_pars_fragment:Hd,lightmap_pars_fragment:Vd,lights_lambert_fragment:Wd,lights_lambert_pars_fragment:Xd,lights_pars_begin:qd,lights_toon_fragment:Kd,lights_toon_pars_fragment:Zd,lights_phong_fragment:$d,lights_phong_pars_fragment:Jd,lights_physical_fragment:Qd,lights_physical_pars_fragment:jd,lights_fragment_begin:eu,lights_fragment_maps:tu,lights_fragment_end:nu,lightprobes_pars_fragment:iu,logdepthbuf_fragment:su,logdepthbuf_pars_fragment:ru,logdepthbuf_pars_vertex:au,logdepthbuf_vertex:ou,map_fragment:lu,map_pars_fragment:cu,map_particle_fragment:du,map_particle_pars_fragment:uu,metalnessmap_fragment:fu,metalnessmap_pars_fragment:hu,morphinstance_vertex:pu,morphcolor_vertex:mu,morphnormal_vertex:gu,morphtarget_pars_vertex:xu,morphtarget_vertex:_u,normal_fragment_begin:vu,normal_fragment_maps:Su,normal_pars_fragment:Mu,normal_pars_vertex:bu,normal_vertex:yu,normalmap_pars_fragment:Eu,clearcoat_normal_fragment_begin:Tu,clearcoat_normal_fragment_maps:wu,clearcoat_pars_fragment:Au,iridescence_pars_fragment:Ru,opaque_fragment:Cu,packing:Pu,premultiplied_alpha_fragment:Lu,project_vertex:Du,dithering_fragment:Iu,dithering_pars_fragment:Nu,roughnessmap_fragment:Uu,roughnessmap_pars_fragment:Fu,shadowmap_pars_fragment:Ou,shadowmap_pars_vertex:Bu,shadowmap_vertex:ku,shadowmask_pars_fragment:zu,skinbase_vertex:Gu,skinning_pars_vertex:Hu,skinning_vertex:Vu,skinnormal_vertex:Wu,specularmap_fragment:Xu,specularmap_pars_fragment:qu,tonemapping_fragment:Yu,tonemapping_pars_fragment:Ku,transmission_fragment:Zu,transmission_pars_fragment:$u,uv_pars_fragment:Ju,uv_pars_vertex:Qu,uv_vertex:ju,worldpos_vertex:ef,background_vert:tf,background_frag:nf,backgroundCube_vert:sf,backgroundCube_frag:rf,cube_vert:af,cube_frag:of,depth_vert:lf,depth_frag:cf,distance_vert:df,distance_frag:uf,equirect_vert:ff,equirect_frag:hf,linedashed_vert:pf,linedashed_frag:mf,meshbasic_vert:gf,meshbasic_frag:xf,meshlambert_vert:_f,meshlambert_frag:vf,meshmatcap_vert:Sf,meshmatcap_frag:Mf,meshnormal_vert:bf,meshnormal_frag:yf,meshphong_vert:Ef,meshphong_frag:Tf,meshphysical_vert:wf,meshphysical_frag:Af,meshtoon_vert:Rf,meshtoon_frag:Cf,points_vert:Pf,points_frag:Lf,shadow_vert:Df,shadow_frag:If,sprite_vert:Nf,sprite_frag:Uf},xe={common:{diffuse:{value:new je(16777215)},opacity:{value:1},map:{value:null},mapTransform:{value:new Ne},alphaMap:{value:null},alphaMapTransform:{value:new Ne},alphaTest:{value:0}},specularmap:{specularMap:{value:null},specularMapTransform:{value:new Ne}},envmap:{envMap:{value:null},envMapRotation:{value:new Ne},reflectivity:{value:1},ior:{value:1.5},refractionRatio:{value:.98},dfgLUT:{value:null}},aomap:{aoMap:{value:null},aoMapIntensity:{value:1},aoMapTransform:{value:new Ne}},lightmap:{lightMap:{value:null},lightMapIntensity:{value:1},lightMapTransform:{value:new Ne}},bumpmap:{bumpMap:{value:null},bumpMapTransform:{value:new Ne},bumpScale:{value:1}},normalmap:{normalMap:{value:null},normalMapTransform:{value:new Ne},normalScale:{value:new Xe(1,1)}},displacementmap:{displacementMap:{value:null},displacementMapTransform:{value:new Ne},displacementScale:{value:1},displacementBias:{value:0}},emissivemap:{emissiveMap:{value:null},emissiveMapTransform:{value:new Ne}},metalnessmap:{metalnessMap:{value:null},metalnessMapTransform:{value:new Ne}},roughnessmap:{roughnessMap:{value:null},roughnessMapTransform:{value:new Ne}},gradientmap:{gradientMap:{value:null}},fog:{fogDensity:{value:25e-5},fogNear:{value:1},fogFar:{value:2e3},fogColor:{value:new je(16777215)}},lights:{ambientLightColor:{value:[]},lightProbe:{value:[]},sunLights:{value:[],properties:{direction:{},color:{}}},sunLightShadows:{value:[],properties:{shadowIntensity:1,shadowBias:{},shadowNormalBias:{},shadowRadius:{},shadowMapSize:{}}},sunShadowMatrix:{value:[]},sunShadowCascade:{value:[]},directionalLights:{value:[],properties:{direction:{},color:{}}},directionalLightShadows:{value:[],properties:{shadowIntensity:1,shadowBias:{},shadowNormalBias:{},shadowRadius:{},shadowMapSize:{}}},directionalShadowMatrix:{value:[]},spotLights:{value:[],properties:{color:{},position:{},direction:{},distance:{},coneCos:{},penumbraCos:{},decay:{}}},spotLightShadows:{value:[],properties:{shadowIntensity:1,shadowBias:{},shadowNormalBias:{},shadowRadius:{},shadowMapSize:{}}},spotLightMap:{value:[]},spotLightMatrix:{value:[]},pointLights:{value:[],properties:{color:{},position:{},decay:{},distance:{}}},pointLightShadows:{value:[],properties:{shadowIntensity:1,shadowBias:{},shadowNormalBias:{},shadowRadius:{},shadowMapSize:{},shadowCameraNear:{},shadowCameraFar:{}}},pointShadowMatrix:{value:[]},hemisphereLights:{value:[],properties:{direction:{},skyColor:{},groundColor:{}}},rectAreaLights:{value:[],properties:{color:{},position:{},width:{},height:{}}},ltc_1:{value:null},ltc_2:{value:null},probesSH:{value:[]},probesMin:{value:[]},probesMax:{value:[]},probesResolution:{value:[]}},points:{diffuse:{value:new je(16777215)},opacity:{value:1},size:{value:1},scale:{value:1},map:{value:null},alphaMap:{value:null},alphaMapTransform:{value:new Ne},alphaTest:{value:0},uvTransform:{value:new Ne}},sprite:{diffuse:{value:new je(16777215)},opacity:{value:1},center:{value:new Xe(.5,.5)},rotation:{value:0},map:{value:null},mapTransform:{value:new Ne},alphaMap:{value:null},alphaMapTransform:{value:new Ne},alphaTest:{value:0}}},fn={basic:{uniforms:kt([xe.common,xe.specularmap,xe.envmap,xe.aomap,xe.lightmap,xe.fog]),vertexShader:Ve.meshbasic_vert,fragmentShader:Ve.meshbasic_frag},lambert:{uniforms:kt([xe.common,xe.specularmap,xe.envmap,xe.aomap,xe.lightmap,xe.emissivemap,xe.bumpmap,xe.normalmap,xe.displacementmap,xe.fog,xe.lights,{emissive:{value:new je(0)},envMapIntensity:{value:1}}]),vertexShader:Ve.meshlambert_vert,fragmentShader:Ve.meshlambert_frag},phong:{uniforms:kt([xe.common,xe.specularmap,xe.envmap,xe.aomap,xe.lightmap,xe.emissivemap,xe.bumpmap,xe.normalmap,xe.displacementmap,xe.fog,xe.lights,{emissive:{value:new je(0)},specular:{value:new je(1118481)},shininess:{value:30},envMapIntensity:{value:1}}]),vertexShader:Ve.meshphong_vert,fragmentShader:Ve.meshphong_frag},standard:{uniforms:kt([xe.common,xe.envmap,xe.aomap,xe.lightmap,xe.emissivemap,xe.bumpmap,xe.normalmap,xe.displacementmap,xe.roughnessmap,xe.metalnessmap,xe.fog,xe.lights,{emissive:{value:new je(0)},roughness:{value:1},metalness:{value:0},envMapIntensity:{value:1}}]),vertexShader:Ve.meshphysical_vert,fragmentShader:Ve.meshphysical_frag},toon:{uniforms:kt([xe.common,xe.aomap,xe.lightmap,xe.emissivemap,xe.bumpmap,xe.normalmap,xe.displacementmap,xe.gradientmap,xe.fog,xe.lights,{emissive:{value:new je(0)}}]),vertexShader:Ve.meshtoon_vert,fragmentShader:Ve.meshtoon_frag},matcap:{uniforms:kt([xe.common,xe.bumpmap,xe.normalmap,xe.displacementmap,xe.fog,{matcap:{value:null}}]),vertexShader:Ve.meshmatcap_vert,fragmentShader:Ve.meshmatcap_frag},points:{uniforms:kt([xe.points,xe.fog]),vertexShader:Ve.points_vert,fragmentShader:Ve.points_frag},dashed:{uniforms:kt([xe.common,xe.fog,{scale:{value:1},dashSize:{value:1},totalSize:{value:2}}]),vertexShader:Ve.linedashed_vert,fragmentShader:Ve.linedashed_frag},depth:{uniforms:kt([xe.common,xe.displacementmap]),vertexShader:Ve.depth_vert,fragmentShader:Ve.depth_frag},normal:{uniforms:kt([xe.common,xe.bumpmap,xe.normalmap,xe.displacementmap,{opacity:{value:1}}]),vertexShader:Ve.meshnormal_vert,fragmentShader:Ve.meshnormal_frag},sprite:{uniforms:kt([xe.sprite,xe.fog]),vertexShader:Ve.sprite_vert,fragmentShader:Ve.sprite_frag},background:{uniforms:{uvTransform:{value:new Ne},t2D:{value:null},backgroundIntensity:{value:1}},vertexShader:Ve.background_vert,fragmentShader:Ve.background_frag},backgroundCube:{uniforms:{envMap:{value:null},backgroundBlurriness:{value:0},backgroundIntensity:{value:1},backgroundRotation:{value:new Ne}},vertexShader:Ve.backgroundCube_vert,fragmentShader:Ve.backgroundCube_frag},cube:{uniforms:{tCube:{value:null},tFlip:{value:-1},opacity:{value:1}},vertexShader:Ve.cube_vert,fragmentShader:Ve.cube_frag},equirect:{uniforms:{tEquirect:{value:null}},vertexShader:Ve.equirect_vert,fragmentShader:Ve.equirect_frag},distance:{uniforms:kt([xe.common,xe.displacementmap,{referencePosition:{value:new z},nearDistance:{value:1},farDistance:{value:1e3}}]),vertexShader:Ve.distance_vert,fragmentShader:Ve.distance_frag},shadow:{uniforms:kt([xe.lights,xe.fog,{color:{value:new je(0)},opacity:{value:1}}]),vertexShader:Ve.shadow_vert,fragmentShader:Ve.shadow_frag}};fn.physical={uniforms:kt([fn.standard.uniforms,{clearcoat:{value:0},clearcoatMap:{value:null},clearcoatMapTransform:{value:new Ne},clearcoatNormalMap:{value:null},clearcoatNormalMapTransform:{value:new Ne},clearcoatNormalScale:{value:new Xe(1,1)},clearcoatRoughness:{value:0},clearcoatRoughnessMap:{value:null},clearcoatRoughnessMapTransform:{value:new Ne},diffuseRoughness:{value:0},diffuseRoughnessMap:{value:null},diffuseRoughnessMapTransform:{value:new Ne},dispersion:{value:0},retroreflectivity:{value:0},iridescence:{value:0},iridescenceMap:{value:null},iridescenceMapTransform:{value:new Ne},iridescenceIOR:{value:1.3},iridescenceThicknessMinimum:{value:100},iridescenceThicknessMaximum:{value:400},iridescenceThicknessMap:{value:null},iridescenceThicknessMapTransform:{value:new Ne},sheen:{value:0},sheenColor:{value:new je(0)},sheenColorMap:{value:null},sheenColorMapTransform:{value:new Ne},sheenRoughness:{value:1},sheenRoughnessMap:{value:null},sheenRoughnessMapTransform:{value:new Ne},transmission:{value:0},transmissionMap:{value:null},transmissionMapTransform:{value:new Ne},transmissionSamplerSize:{value:new Xe},transmissionSamplerMap:{value:null},thickness:{value:0},thicknessMap:{value:null},thicknessMapTransform:{value:new Ne},attenuationDistance:{value:0},attenuationColor:{value:new je(0)},specularColor:{value:new je(1,1,1)},specularColorMap:{value:null},specularColorMapTransform:{value:new Ne},specularIntensity:{value:1},specularIntensityMap:{value:null},specularIntensityMapTransform:{value:new Ne},anisotropyVector:{value:new Xe},anisotropyMap:{value:null},anisotropyMapTransform:{value:new Ne}}]),vertexShader:Ve.meshphysical_vert,fragmentShader:Ve.meshphysical_frag};const cs={r:0,b:0,g:0},Ff=new mt,cl=new Ne;cl.set(-1,0,0,0,1,0,0,0,1);function Of(i,e,t,n,s,r){const a=new je(0);let o=s===!0?0:1,d,c,m=null,x=0,f=null;function g(b){let y=b.isScene===!0?b.background:null;if(y&&y.isTexture){const E=b.backgroundBlurriness>0;y=e.get(y,E)}return y}function l(b){let y=!1;const E=g(b);E===null?p(a,o):E&&E.isColor&&(p(E,1),y=!0);const R=i.xr.getEnvironmentBlendMode();R==="additive"?t.buffers.color.setClear(0,0,0,1,r):R==="alpha-blend"&&t.buffers.color.setClear(0,0,0,0,r),(i.autoClear||y)&&(t.buffers.depth.setTest(!0),t.buffers.depth.setMask(!0),t.buffers.color.setMask(!0),i.clear(i.autoClearColor,i.autoClearDepth,i.autoClearStencil))}function T(b,y){const E=g(y);E&&E.isCubeTexture?(c===void 0&&(c=new ht(new an(1,1,1),new jn({name:"BackgroundCubeMaterial",uniforms:Si(fn.backgroundCube.uniforms),vertexShader:fn.backgroundCube.vertexShader,fragmentShader:fn.backgroundCube.fragmentShader,side:Xt,depthTest:!1,depthWrite:!1,fog:!1,allowOverride:!1})),c.geometry.deleteAttribute("normal"),c.geometry.deleteAttribute("uv"),c.onBeforeRender=function(R,A,h){this.matrixWorld.copyPosition(h.matrixWorld)},Object.defineProperty(c.material,"envMap",{get:function(){return this.uniforms.envMap.value}}),n.update(c)),c.material.uniforms.envMap.value=E,c.material.uniforms.backgroundBlurriness.value=y.backgroundBlurriness,c.material.uniforms.backgroundIntensity.value=y.backgroundIntensity,c.material.uniforms.backgroundRotation.value.setFromMatrix4(Ff.makeRotationFromEuler(y.backgroundRotation)).transpose(),E.isRenderTargetTexture===!1&&c.material.uniforms.backgroundRotation.value.premultiply(cl),c.material.toneMapped=Ze.getTransfer(E.colorSpace)!==at,(m!==E||x!==E.version||f!==i.toneMapping)&&(c.material.needsUpdate=!0,m=E,x=E.version,f=i.toneMapping),c.layers.enableAll(),b.unshift(c,c.geometry,c.material,0,0,null)):E&&E.isTexture&&(d===void 0&&(d=new ht(new Cs(2,2),new jn({name:"BackgroundMaterial",uniforms:Si(fn.background.uniforms),vertexShader:fn.background.vertexShader,fragmentShader:fn.background.fragmentShader,side:Jn,depthTest:!1,depthWrite:!1,fog:!1,allowOverride:!1})),d.geometry.deleteAttribute("normal"),Object.defineProperty(d.material,"map",{get:function(){return this.uniforms.t2D.value}}),n.update(d)),d.material.uniforms.t2D.value=E,d.material.uniforms.backgroundIntensity.value=y.backgroundIntensity,d.material.toneMapped=Ze.getTransfer(E.colorSpace)!==at,E.matrixAutoUpdate===!0&&E.updateMatrix(),d.material.uniforms.uvTransform.value.copy(E.matrix),(m!==E||x!==E.version||f!==i.toneMapping)&&(d.material.needsUpdate=!0,m=E,x=E.version,f=i.toneMapping),d.layers.enableAll(),b.unshift(d,d.geometry,d.material,0,0,null))}function p(b,y){b.getRGB(cs,sl(i)),t.buffers.color.setClear(cs.r,cs.g,cs.b,y,r)}function u(){c!==void 0&&(c.geometry.dispose(),c.material.dispose(),c=void 0),d!==void 0&&(d.geometry.dispose(),d.material.dispose(),d=void 0)}return{getClearColor:function(){return a},setClearColor:function(b,y=1){a.set(b),o=y,p(a,o)},getClearAlpha:function(){return o},setClearAlpha:function(b){o=b,p(a,o)},render:l,addToRenderList:T,dispose:u}}function Bf(i,e){const t=i.getParameter(i.MAX_VERTEX_ATTRIBS),n={},s=f(null);let r=s,a=!1;function o(U,F,X,L,W){let K=!1;const H=x(U,L,X,F);r!==H&&(r=H,c(r.object)),K=g(U,L,X,W),K&&l(U,L,X,W),W!==null&&e.update(W,i.ELEMENT_ARRAY_BUFFER),(K||a)&&(a=!1,E(U,F,X,L),W!==null&&i.bindBuffer(i.ELEMENT_ARRAY_BUFFER,e.get(W).buffer))}function d(){return i.createVertexArray()}function c(U){return i.bindVertexArray(U)}function m(U){return i.deleteVertexArray(U)}function x(U,F,X,L){const W=L.wireframe===!0;let K=n[F.id];K===void 0&&(K={},n[F.id]=K);const H=U.isInstancedMesh===!0?U.id:0;let ne=K[H];ne===void 0&&(ne={},K[H]=ne);let q=ne[X.id];q===void 0&&(q={},ne[X.id]=q);let ee=q[W];return ee===void 0&&(ee=f(d()),q[W]=ee),ee}function f(U){const F=[],X=[],L=[];for(let W=0;W<t;W++)F[W]=0,X[W]=0,L[W]=0;return{geometry:null,program:null,wireframe:!1,newAttributes:F,enabledAttributes:X,attributeDivisors:L,object:U,attributes:{},index:null}}function g(U,F,X,L){const W=r.attributes,K=F.attributes;let H=0;const ne=X.getAttributes();for(const q in ne)if(ne[q].location>=0){const te=W[q];let Me=K[q];if(Me===void 0&&(q==="instanceMatrix"&&U.instanceMatrix&&(Me=U.instanceMatrix),q==="instanceColor"&&U.instanceColor&&(Me=U.instanceColor)),te===void 0||te.attribute!==Me||Me&&te.data!==Me.data)return!0;H++}return r.attributesNum!==H||r.index!==L}function l(U,F,X,L){const W={},K=F.attributes;let H=0;const ne=X.getAttributes();for(const q in ne)if(ne[q].location>=0){let te=K[q];te===void 0&&(q==="instanceMatrix"&&U.instanceMatrix&&(te=U.instanceMatrix),q==="instanceColor"&&U.instanceColor&&(te=U.instanceColor));const Me={};Me.attribute=te,te&&te.data&&(Me.data=te.data),W[q]=Me,H++}r.attributes=W,r.attributesNum=H,r.index=L}function T(){const U=r.newAttributes;for(let F=0,X=U.length;F<X;F++)U[F]=0}function p(U){u(U,0)}function u(U,F){const X=r.newAttributes,L=r.enabledAttributes,W=r.attributeDivisors;X[U]=1,L[U]===0&&(i.enableVertexAttribArray(U),L[U]=1),W[U]!==F&&(i.vertexAttribDivisor(U,F),W[U]=F)}function b(){const U=r.newAttributes,F=r.enabledAttributes;for(let X=0,L=F.length;X<L;X++)F[X]!==U[X]&&(i.disableVertexAttribArray(X),F[X]=0)}function y(U,F,X,L,W,K,H){H===!0?i.vertexAttribIPointer(U,F,X,W,K):i.vertexAttribPointer(U,F,X,L,W,K)}function E(U,F,X,L){T();const W=L.attributes,K=X.getAttributes(),H=F.defaultAttributeValues;for(const ne in K){const q=K[ne];if(q.location>=0){let ee=W[ne];if(ee===void 0&&(ne==="instanceMatrix"&&U.instanceMatrix&&(ee=U.instanceMatrix),ne==="instanceColor"&&U.instanceColor&&(ee=U.instanceColor)),ee!==void 0){const te=ee.normalized,Me=ee.itemSize,Le=e.get(ee);if(Le===void 0)continue;const $=Le.buffer,pe=Le.type,ve=Le.bytesPerElement,Ae=pe===i.INT||pe===i.UNSIGNED_INT||ee.gpuType===ea;if(ee.isInterleavedBufferAttribute){const me=ee.data,Ie=me.isInterleavedBuffer===!0?me.stride:me.itemSize,gt=ee.offset;if(me.isInstancedInterleavedBuffer||me.isInstancedBufferAttribute){for(let Re=0;Re<q.locationSize;Re++)u(q.location+Re,me.meshPerAttribute);U.isInstancedMesh!==!0&&L._maxInstanceCount===void 0&&(L._maxInstanceCount=me.meshPerAttribute*me.count)}else for(let Re=0;Re<q.locationSize;Re++)p(q.location+Re);i.bindBuffer(i.ARRAY_BUFFER,$);for(let Re=0;Re<q.locationSize;Re++)y(q.location+Re,Me/q.locationSize,pe,te,Ie*ve,(gt+Me/q.locationSize*Re)*ve,Ae)}else{if(ee.isInstancedBufferAttribute){for(let me=0;me<q.locationSize;me++)u(q.location+me,ee.meshPerAttribute);U.isInstancedMesh!==!0&&L._maxInstanceCount===void 0&&(L._maxInstanceCount=ee.meshPerAttribute*ee.count)}else for(let me=0;me<q.locationSize;me++)p(q.location+me);i.bindBuffer(i.ARRAY_BUFFER,$);for(let me=0;me<q.locationSize;me++)y(q.location+me,Me/q.locationSize,pe,te,Me*ve,Me/q.locationSize*me*ve,Ae)}}else if(H!==void 0){const te=H[ne];if(te!==void 0)switch(te.length){case 2:i.vertexAttrib2fv(q.location,te);break;case 3:i.vertexAttrib3fv(q.location,te);break;case 4:i.vertexAttrib4fv(q.location,te);break;default:i.vertexAttrib1fv(q.location,te)}}}}b()}function R(){I();for(const U in n){const F=n[U];for(const X in F){const L=F[X];for(const W in L){const K=L[W];for(const H in K)m(K[H].object),delete K[H];delete L[W]}}delete n[U]}}function A(U){if(n[U.id]===void 0)return;const F=n[U.id];for(const X in F){const L=F[X];for(const W in L){const K=L[W];for(const H in K)m(K[H].object),delete K[H];delete L[W]}}delete n[U.id]}function h(U){for(const F in n){const X=n[F];for(const L in X){const W=X[L];if(W[U.id]===void 0)continue;const K=W[U.id];for(const H in K)m(K[H].object),delete K[H];delete W[U.id]}}}function M(U){for(const F in n){const X=n[F],L=U.isInstancedMesh===!0?U.id:0,W=X[L];if(W!==void 0){for(const K in W){const H=W[K];for(const ne in H)m(H[ne].object),delete H[ne];delete W[K]}delete X[L],Object.keys(X).length===0&&delete n[F]}}}function I(){C(),a=!0,r!==s&&(r=s,c(r.object))}function C(){s.geometry=null,s.program=null,s.wireframe=!1}return{setup:o,reset:I,resetDefaultState:C,dispose:R,releaseStatesOfGeometry:A,releaseStatesOfObject:M,releaseStatesOfProgram:h,initAttributes:T,enableAttribute:p,disableUnusedAttributes:b}}function kf(i,e,t){let n;function s(d){n=d}function r(d,c){i.drawArrays(n,d,c),t.update(c,n,1)}function a(d,c,m){m!==0&&(i.drawArraysInstanced(n,d,c,m),t.update(c,n,m))}function o(d,c,m){if(m===0)return;e.get("WEBGL_multi_draw").multiDrawArraysWEBGL(n,d,0,c,0,m);let f=0;for(let g=0;g<m;g++)f+=c[g];t.update(f,n,1)}this.setMode=s,this.render=r,this.renderInstances=a,this.renderMultiDraw=o}function zf(i,e,t,n){let s;function r(){if(s!==void 0)return s;if(e.has("EXT_texture_filter_anisotropic")===!0){const h=e.get("EXT_texture_filter_anisotropic");s=i.getParameter(h.MAX_TEXTURE_MAX_ANISOTROPY_EXT)}else s=0;return s}function a(h){return!(h!==pn&&n.convert(h)!==i.getParameter(i.IMPLEMENTATION_COLOR_READ_FORMAT))}function o(h){const M=h===xn&&(e.has("EXT_color_buffer_half_float")||e.has("EXT_color_buffer_float"));return!(h!==Zt&&h!==hn&&!M&&n.convert(h)!==i.getParameter(i.IMPLEMENTATION_COLOR_READ_TYPE))}function d(h){if(h==="highp"){if(i.getShaderPrecisionFormat(i.VERTEX_SHADER,i.HIGH_FLOAT).precision>0&&i.getShaderPrecisionFormat(i.FRAGMENT_SHADER,i.HIGH_FLOAT).precision>0)return"highp";h="mediump"}return h==="mediump"&&i.getShaderPrecisionFormat(i.VERTEX_SHADER,i.MEDIUM_FLOAT).precision>0&&i.getShaderPrecisionFormat(i.FRAGMENT_SHADER,i.MEDIUM_FLOAT).precision>0?"mediump":"lowp"}let c=t.precision!==void 0?t.precision:"highp";const m=d(c);m!==c&&(De("WebGLRenderer:",c,"not supported, using",m,"instead."),c=m);const x=t.reversedDepthBuffer===!0&&e.has("EXT_clip_control"),f=t.logarithmicDepthBuffer===!0&&x===!1;t.reversedDepthBuffer===!0&&x===!1&&De("WebGLRenderer: Unable to use reversed depth buffer due to missing EXT_clip_control extension. Fallback to default depth buffer."),t.logarithmicDepthBuffer===!0&&x===!0&&De("WebGLRenderer: Logarithmic depth buffer is ignored when reversed depth buffer is enabled.");const g=i.getParameter(i.MAX_TEXTURE_IMAGE_UNITS),l=i.getParameter(i.MAX_VERTEX_TEXTURE_IMAGE_UNITS),T=i.getParameter(i.MAX_TEXTURE_SIZE),p=i.getParameter(i.MAX_CUBE_MAP_TEXTURE_SIZE),u=i.getParameter(i.MAX_VERTEX_ATTRIBS),b=i.getParameter(i.MAX_VERTEX_UNIFORM_VECTORS),y=i.getParameter(i.MAX_VARYING_VECTORS),E=i.getParameter(i.MAX_FRAGMENT_UNIFORM_VECTORS),R=i.getParameter(i.MAX_SAMPLES),A=i.getParameter(i.SAMPLES);return{isWebGL2:!0,getMaxAnisotropy:r,getMaxPrecision:d,textureFormatReadable:a,textureTypeReadable:o,precision:c,logarithmicDepthBuffer:f,reversedDepthBuffer:x,maxTextures:g,maxVertexTextures:l,maxTextureSize:T,maxCubemapSize:p,maxAttributes:u,maxVertexUniforms:b,maxVaryings:y,maxFragmentUniforms:E,maxSamples:R,samples:A}}function Gf(i){const e=this;let t=null,n=0,s=!1,r=!1;const a=new Un,o=new Ne,d={value:null,needsUpdate:!1};this.uniform=d,this.numPlanes=0,this.numIntersection=0,this.init=function(x,f){const g=x.length!==0||f||n!==0||s;return s=f,n=x.length,g},this.beginShadows=function(){r=!0,m(null)},this.endShadows=function(){r=!1},this.setGlobalState=function(x,f){t=m(x,f,0)},this.setState=function(x,f,g){const l=x.clippingPlanes,T=x.clipIntersection,p=x.clipShadows,u=i.get(x);if(!s||l===null||l.length===0||r&&!p)r?m(null):c();else{const b=r?0:n,y=b*4;let E=u.clippingState||null;d.value=E,E=m(l,f,y,g);for(let R=0;R!==y;++R)E[R]=t[R];u.clippingState=E,this.numIntersection=T?this.numPlanes:0,this.numPlanes+=b}};function c(){d.value!==t&&(d.value=t,d.needsUpdate=n>0),e.numPlanes=n,e.numIntersection=0}function m(x,f,g,l){const T=x!==null?x.length:0;let p=null;if(T!==0){if(p=d.value,l!==!0||p===null){const u=g+T*4,b=f.matrixWorldInverse;o.getNormalMatrix(b),(p===null||p.length<u)&&(p=new Float32Array(u));for(let y=0,E=g;y!==T;++y,E+=4)a.copy(x[y]).applyMatrix4(b,o),a.normal.toArray(p,E),p[E+3]=a.constant}d.value=p,d.needsUpdate=!0}return e.numPlanes=T,e.numIntersection=0,p}}class ha extends mn{constructor(e=1,t={}){super(e,e,t),this.isWebGLCubeRenderTarget=!0;const n={width:e,height:e,depth:1},s=[n,n,n,n,n,n];this.texture=new tl(s),this._setTextureOptions(t),this.texture.isRenderTargetTexture=!0}fromEquirectangularTexture(e,t){this.texture.type=t.type,this.texture.colorSpace=t.colorSpace,this.texture.generateMipmaps=t.generateMipmaps,this.texture.minFilter=t.minFilter,this.texture.magFilter=t.magFilter;const n={uniforms:{tEquirect:{value:null}},vertexShader:`

				varying vec3 vWorldDirection;

				vec3 transformDirection( in vec3 dir, in mat4 matrix ) {

					return normalize( ( matrix * vec4( dir, 0.0 ) ).xyz );

				}

				void main() {

					vWorldDirection = transformDirection( position, modelMatrix );

					#include <begin_vertex>
					#include <project_vertex>

				}
			`,fragmentShader:`

				uniform sampler2D tEquirect;

				varying vec3 vWorldDirection;

				#include <common>

				void main() {

					vec3 direction = normalize( vWorldDirection );

					vec2 sampleUV = equirectUv( direction );

					gl_FragColor = texture2D( tEquirect, sampleUV );

				}
			`},s=new an(5,5,5),r=new jn({name:"CubemapFromEquirect",uniforms:Si(n.uniforms),vertexShader:n.vertexShader,fragmentShader:n.fragmentShader,side:Xt,blending:mi});r.uniforms.tEquirect.value=t;const a=new ht(s,r),o=t.minFilter;return t.minFilter===On&&(t.minFilter=Wt),new $r(1,10,this).update(e,a),t.minFilter=o,a.geometry.dispose(),a.material.dispose(),this}clear(e,t=!0,n=!0,s=!0){const r=e.getRenderTarget();for(let a=0;a<6;a++)e.setRenderTarget(this,a),e.clear(t,n,s);e.setRenderTarget(r)}}const ro=256,Hf=3,Vf=20,dl=256,ul=16,Wf=3,Xf=new z,qf=new je;class As{constructor(e){this._renderer=e,this._cubeSize=0,this._sourceTarget=null,this._cubeCamera=new $r(1,10,null),this._boxMesh=new ht(new an(5,5,5),null),this._cubemapMaterial=null,this._equirectMaterial=null,this._blurMaterial=null,this._ggxMaterial=null,this._integrationMaterial=null}fromScene(e,t=0,n=.1,s=100,r={}){const{size:a=256,position:o=Xf}=r,d=this._renderer;this._setSize(a);const c=this._allocateTarget(),m=this._getSourceTarget(!0);t>0&&(m.texture.mipmapsAutoUpdate=!1);const x=d.autoClear,f=d.autoClearColor,g=d.autoClearDepth,l=d.autoClearStencil,T=e.background;d.autoClear=!0,d.autoClearColor=!0,d.autoClearDepth=!0,d.autoClearStencil=!0,T===null&&(e.background=d.getClearColor(qf));const p=new $r(n,s,m);return p.position.copy(o),p.update(d,e),d.autoClear=x,d.autoClearColor=f,d.autoClearDepth=g,d.autoClearStencil=l,e.background=T,t>0&&(m.texture.mipmapsAutoUpdate=!0,this._blur(c,t)),this._applyPMREM(c),c}fromEquirectangular(e,t=null){return this._fromTexture(e,t)}fromCubemap(e,t=null){return this._fromTexture(e,t)}compileCubemapShader(){this._cubemapMaterial===null&&(this._cubemapMaterial=lo(),this._compileMaterial(this._cubemapMaterial))}compileEquirectangularShader(){this._equirectMaterial===null&&(this._equirectMaterial=oo(),this._compileMaterial(this._equirectMaterial))}dispose(){this._sourceTarget!==null&&this._sourceTarget.dispose(),this._cubemapMaterial!==null&&this._cubemapMaterial.dispose(),this._equirectMaterial!==null&&this._equirectMaterial.dispose(),this._blurMaterial!==null&&this._blurMaterial.dispose(),this._ggxMaterial!==null&&this._ggxMaterial.dispose(),this._integrationMaterial!==null&&this._integrationMaterial.dispose(),this._boxMesh.geometry.dispose()}static lodToRoughness(e,t){return t>0?1-Math.sqrt(1-e/t):0}_setSize(e){this._cubeSize=Math.max(ro,hc(e))}_fromTexture(e,t){e.mapping===xi||e.mapping===Ii?this._setSize(e.image.length===0?ro:e.image[0].width||e.image[0].image.width):this._setSize(e.image.width/4);const n=t||this._allocateTarget();return this._textureToCubemap(e),this._applyPMREM(n),n}_allocateTarget(){const e=this._cubeSize,t=ao(e,!1,!1),n=Math.log2(e)-Hf;for(let s=0;s<=n;s++)t.texture.mipmaps.push({width:e>>s,height:e>>s});return t.texture.name="PMREM",t.texture.isPMREMTexture=!0,t}_getSourceTarget(e=!1){const t=this._cubeSize,n=this._sourceTarget;return(n===null||n.width!==t||e&&n.depthBuffer===!1)&&(n!==null&&n.dispose(),this._sourceTarget=ao(t,!0,e)),this._sourceTarget}_compileMaterial(e){this._boxMesh.material=e,this._renderer.compile(this._boxMesh,this._cubeCamera.children[0])}_renderCube(e,t,n){const s=this._boxMesh,r=this._cubeCamera;s.material=n,r.renderTarget=e,r.activeMipmapLevel=t;const a=e.width>>t;e.viewport.set(0,0,a,a),r.update(this._renderer,s),e.viewport.set(0,0,e.width,e.height)}_textureToCubemap(e){let t;e.mapping===xi||e.mapping===Ii?(this._cubemapMaterial===null&&(this._cubemapMaterial=lo()),t=this._cubemapMaterial,t.uniforms.flipEnvMap.value=e.isRenderTargetTexture===!1?-1:1):(this._equirectMaterial===null&&(this._equirectMaterial=oo()),t=this._equirectMaterial),t.uniforms.envMap.value=e,this._renderCube(this._getSourceTarget(),0,t)}_applyPMREM(e){this._ggxMaterial===null&&(this._ggxMaterial=Yf(),this._integrationMaterial=Kf());const t=this._cubeSize,n=e.texture.mipmaps.length-1,s=this._ggxMaterial.uniforms;s.envMap.value=this._sourceTarget.texture;const r=this._integrationMaterial.uniforms;r.envMap.value=this._sourceTarget.texture,r.sourceLod.value=Math.log2(t/ul);for(let a=0;a<=n;a++){const o=As.lodToRoughness(a,n);if(a>n-Wf)r.roughness.value=o,this._renderCube(e,a,this._integrationMaterial);else{const d=o>0?Math.log2(t)+.5*Math.log2(6/(dl*Math.pow(o,4)))+.5:0;s.roughness.value=o,s.lodBias.value=d,this._renderCube(e,a,this._ggxMaterial)}}}_blur(e,t){this._blurMaterial===null&&(this._blurMaterial=Zf());const n=this._blurMaterial,s=n.uniforms,r=this._sourceTarget;s.sigma.value=Math.min(t,Math.PI)/Math.SQRT2,s.envMap.value=r.texture,this._renderCube(e,0,n),s.envMap.value=e.texture,this._renderCube(r,0,n)}}function ao(i,e,t){return new ha(i,{minFilter:On,generateMipmaps:e,type:xn,colorSpace:Ms,depthBuffer:t})}function zi(i,e,t){return new jn({name:i,uniforms:e,vertexShader:Ve.cube_vert,fragmentShader:t,side:Xt,blending:mi,depthTest:!1,depthWrite:!1})}function Yf(){return zi("PMREMGGXConvolution",{envMap:{value:null},roughness:{value:0},lodBias:{value:0}},`

		#define GGX_SAMPLES ${dl}u

		varying vec3 vWorldDirection;

		uniform samplerCube envMap;
		uniform float roughness;
		uniform float lodBias;

		#include <common>

		// Van der Corput radical inverse
		float radicalInverse_VdC( uint bits ) {

			bits = ( bits << 16u ) | ( bits >> 16u );
			bits = ( ( bits & 0x55555555u ) << 1u ) | ( ( bits & 0xAAAAAAAAu ) >> 1u );
			bits = ( ( bits & 0x33333333u ) << 2u ) | ( ( bits & 0xCCCCCCCCu ) >> 2u );
			bits = ( ( bits & 0x0F0F0F0Fu ) << 4u ) | ( ( bits & 0xF0F0F0F0u ) >> 4u );
			bits = ( ( bits & 0x00FF00FFu ) << 8u ) | ( ( bits & 0xFF00FF00u ) >> 8u );
			return float( bits ) * 2.3283064365386963e-10; // / 0x100000000

		}

		// Hammersley sequence
		vec2 hammersley( uint i, uint N ) {

			return vec2( float( i ) / float( N ), radicalInverse_VdC( i ) );

		}

		void main() {

			vec3 N = normalize( vWorldDirection );

			// For very low roughness, just sample the environment directly
			if ( roughness < 0.001 ) {

				gl_FragColor = vec4( textureLod( envMap, N, 0.0 ).rgb, 1.0 );
				return;

			}

			float alpha = roughness * roughness;
			float alpha2 = alpha * alpha;

			// Tangent space basis for VNDF sampling
			vec3 up = abs( N.z ) < 0.999 ? vec3( 0.0, 0.0, 1.0 ) : vec3( 1.0, 0.0, 0.0 );
			vec3 tangent = normalize( cross( up, N ) );
			vec3 bitangent = cross( N, tangent );

			vec3 prefilteredColor = vec3( 0.0 );
			float totalWeight = 0.0;

			for ( uint i = 0u; i < GGX_SAMPLES; i ++ ) {

				vec2 Xi = hammersley( i, GGX_SAMPLES );

				// With V = N, sample the reflected direction directly.
				float invQ = 1.0 / ( 1.0 - Xi.x + alpha2 * Xi.x );
				float NdotL = ( 1.0 - Xi.x - alpha2 * Xi.x ) * invQ;

				if ( NdotL > 0.0 ) {

					float phi = 2.0 * PI * Xi.y;
					float sinTheta = 2.0 * alpha * sqrt( Xi.x * ( 1.0 - Xi.x ) ) * invQ;
					vec3 L = N * NdotL + ( tangent * cos( phi ) + bitangent * sin( phi ) ) * sinTheta;

					// Match the source mip to the sample's solid angle. The terms
					// independent of the sample are precomputed in lodBias.
					float d = alpha2 * invQ;
					float lod = max( log2( d ) + lodBias, 0.0 );

					// Weight by NdotL for the split-sum approximation
					prefilteredColor += textureLod( envMap, L, lod ).rgb * NdotL;
					totalWeight += NdotL;

				}

			}

			gl_FragColor = vec4( prefilteredColor / totalWeight, 1.0 );

		}
	`)}function Kf(){return zi("PMREMGGXIntegration",{envMap:{value:null},roughness:{value:0},sourceLod:{value:0},sourceSize:{value:ul}},`

		varying vec3 vWorldDirection;

		uniform samplerCube envMap;
		uniform float roughness;
		uniform float sourceLod;
		uniform int sourceSize;

		void main() {

			vec3 N = normalize( vWorldDirection );

			float alpha = roughness * roughness;
			float alpha2 = alpha * alpha;

			float texelSize = 2.0 / float( sourceSize );

			vec3 prefilteredColor = vec3( 0.0 );
			float totalWeight = 0.0;

			// Pair opposite texels: only the one in N's hemisphere contributes.
			for ( int face = 0; face < 3; face ++ ) {

				for ( int y = 0; y < sourceSize; y ++ ) {

					for ( int x = 0; x < sourceSize; x ++ ) {

						vec2 uv = ( vec2( x, y ) + 0.5 ) * texelSize - 1.0;
						vec3 texelDirection = face == 0 ? vec3( 1.0, uv ) : ( face == 1 ? vec3( uv.x, 1.0, uv.y ) : vec3( uv, 1.0 ) );

						float invDistance = inversesqrt( 1.0 + dot( uv, uv ) );
						float NdotL = dot( N, texelDirection );
						texelDirection *= NdotL < 0.0 ? - 1.0 : 1.0;
						NdotL = abs( NdotL ) * invDistance;

						// With V = N, NdotH squared is ( 1 + NdotL ) / 2. Common factors
						// in the GGX distribution and texel solid angle cancel when normalized.
						float d = 1.0 + alpha2 + ( alpha2 - 1.0 ) * NdotL;
						float weight = NdotL * invDistance * invDistance * invDistance / ( d * d );

						prefilteredColor += textureLod( envMap, texelDirection, sourceLod ).rgb * weight;
						totalWeight += weight;

					}

				}

			}

			gl_FragColor = vec4( prefilteredColor / totalWeight, 1.0 );

		}
	`)}function Zf(){return zi("PMREMSphericalGaussianBlur",{envMap:{value:null},sigma:{value:0}},`

		#define SAMPLES ${Vf}
		#define GOLDEN_ANGLE 2.39996322973

		varying vec3 vWorldDirection;

		uniform samplerCube envMap;
		uniform float sigma;

		#include <common>

		void main() {

			vec3 outputDirection = normalize( vWorldDirection );

			vec3 up = abs( outputDirection.z ) < 0.999 ? vec3( 0.0, 0.0, 1.0 ) : vec3( 1.0, 0.0, 0.0 );
			vec3 tangent = normalize( cross( up, outputDirection ) );
			vec3 bitangent = cross( outputDirection, tangent );

			// Truncate the kernel at three standard deviations or at the antipode.
			float thetaMax = min( 3.0 * sigma, PI );
			float truncation = 1.0 - exp( - 0.5 * thetaMax * thetaMax / ( sigma * sigma ) );

			vec3 accumColor = vec3( 0.0 );
			float accumWeight = 0.0;

			for ( int i = 0; i < SAMPLES; i ++ ) {

				// Stratified inverse-CDF sampling of the Gaussian, placed on a golden-angle spiral.
				float stratum = ( float( i ) + 0.5 ) / float( SAMPLES );
				float theta = sigma * sqrt( - 2.0 * log( 1.0 - stratum * truncation ) );
				float phi = float( i ) * GOLDEN_ANGLE;

				vec3 offset = cos( phi ) * tangent + sin( phi ) * bitangent;
				vec3 sampleDirection = cos( theta ) * outputDirection + sin( theta ) * offset;

				// Correct the planar sample density to solid angle.
				float weight = sin( theta ) / theta;

				accumColor += weight * textureLod( envMap, sampleDirection, 0.0 ).rgb;
				accumWeight += weight;

			}

			gl_FragColor = vec4( accumColor / accumWeight, 1.0 );

		}
	`)}function oo(){return zi("PMREMEquirectangularToCubemap",{envMap:{value:null}},`

		varying vec3 vWorldDirection;

		uniform sampler2D envMap;

		#include <common>

		void main() {

			// Average four subpixel samples to preserve small, bright features.
			vec3 direction = normalize( vWorldDirection );
			vec3 dx = dFdx( direction ) * 0.25;
			vec3 dy = dFdy( direction ) * 0.25;

			vec3 color = textureLod( envMap, equirectUv( normalize( direction - dx - dy ) ), 0.0 ).rgb;
			color += textureLod( envMap, equirectUv( normalize( direction + dx - dy ) ), 0.0 ).rgb;
			color += textureLod( envMap, equirectUv( normalize( direction - dx + dy ) ), 0.0 ).rgb;
			color += textureLod( envMap, equirectUv( normalize( direction + dx + dy ) ), 0.0 ).rgb;
			gl_FragColor = vec4( color * 0.25, 1.0 );

		}
	`)}function lo(){return zi("PMREMCubemapToCubemap",{envMap:{value:null},flipEnvMap:{value:-1}},`

		varying vec3 vWorldDirection;

		uniform samplerCube envMap;
		uniform float flipEnvMap;

		void main() {

			gl_FragColor = vec4( textureCube( envMap, vec3( flipEnvMap * vWorldDirection.x, vWorldDirection.yz ) ).rgb, 1.0 );

		}
	`)}function $f(i){let e=new WeakMap,t=new WeakMap,n=null;function s(f,g=!1){return f==null?null:g?a(f):r(f)}function r(f){if(f&&f.isTexture){const g=f.mapping;if(g===Ns||g===Us)if(e.has(f)){const l=e.get(f).texture;return o(l,f.mapping)}else{const l=f.image;if(l&&l.height>0){const T=new ha(l.height);return T.fromEquirectangularTexture(i,f),e.set(f,T),f.addEventListener("dispose",c),o(T.texture,f.mapping)}else return null}}return f}function a(f){if(f&&f.isTexture&&f.isPMREMTexture!==!0){const g=f.mapping,l=g===Ns||g===Us,T=g===xi||g===Ii;if(l||T){let p=t.get(f);const u=p!==void 0?p.texture.pmremVersion:0;if(f.isRenderTargetTexture&&f.pmremVersion!==u)return n===null&&(n=new As(i)),p=l?n.fromEquirectangular(f,p):n.fromCubemap(f,p),p.texture.pmremVersion=f.pmremVersion,t.set(f,p),p.texture;if(p!==void 0)return p.texture;{const b=f.image;return l&&b&&b.height>0||T&&b&&d(b)?(n===null&&(n=new As(i)),p=l?n.fromEquirectangular(f):n.fromCubemap(f),p.texture.pmremVersion=f.pmremVersion,t.set(f,p),f.addEventListener("dispose",m),p.texture):null}}}return f}function o(f,g){return g===Ns?f.mapping=xi:g===Us&&(f.mapping=Ii),f}function d(f){let g=0;const l=6;for(let T=0;T<l;T++)f[T]!==void 0&&g++;return g===l}function c(f){const g=f.target;g.removeEventListener("dispose",c);const l=e.get(g);l!==void 0&&(e.delete(g),l.dispose())}function m(f){const g=f.target;g.removeEventListener("dispose",m);const l=t.get(g);l!==void 0&&(t.delete(g),l.dispose())}function x(){e=new WeakMap,t=new WeakMap,n!==null&&(n.dispose(),n=null)}return{get:s,dispose:x}}function Jf(i){const e={};function t(n){if(e[n]!==void 0)return e[n];const s=i.getExtension(n);return e[n]=s,s}return{has:function(n){return t(n)!==null},init:function(){t("EXT_color_buffer_float"),t("WEBGL_clip_cull_distance"),t("OES_texture_float_linear"),t("EXT_color_buffer_half_float"),t("WEBGL_multisampled_render_to_texture"),t("WEBGL_render_shared_exponent")},get:function(n){const s=t(n);return s===null&&xs("WebGLRenderer: "+n+" extension not supported."),s}}}function Qf(i,e,t,n){const s={},r=new WeakMap,a=new FinalizationRegistry(l=>delete s[l]);function o(l){d(l.target)}function d(l){l.index!==null&&e.remove(l.index);for(const p in l.attributes)e.remove(l.attributes[p]);l.removeEventListener("dispose",o),delete s[l.id],a.unregister(l);const T=r.get(l);T&&(e.remove(T),r.delete(l)),n.releaseStatesOfGeometry(l),l.isInstancedBufferGeometry===!0&&delete l._maxInstanceCount,t.memory.geometries--}function c(l,T){return s[T.id]!==void 0||(T.addEventListener("dispose",o),s[T.id]=new WeakRef(T),a.register(T,T.id,T),t.memory.geometries++),T}function m(l){const T=l.attributes;for(const p in T)e.update(T[p],i.ARRAY_BUFFER)}function x(l){const T=l.index,p=l.attributes.position;let u=0;if(p===void 0)return;const b=T!==null?T.array.length:p.array.length/3-1,y=p.count>=65535?el:jo,E=new y(Math.ceil(b/3)*6,1),R=E.array;if(T!==null){const h=T.array;u=T.version;for(let M=0,I=0;M<b;M+=3){const C=h[M+0],U=h[M+1],F=h[M+2];R[I++]=C,R[I++]=U,R[I++]=U,R[I++]=F,R[I++]=F,R[I++]=C}}else{u=p.version;for(let h=0,M=0;h<b;h+=3){const I=h+0,C=h+1,U=h+2;R[M++]=I,R[M++]=C,R[M++]=C,R[M++]=U,R[M++]=U,R[M++]=I}}E.version=u;const A=r.get(l);A&&e.remove(A),r.set(l,E)}function f(l){const T=r.get(l);if(T){const p=l.index;p!==null&&T.version<p.version&&x(l)}else x(l);return r.get(l)}function g(){for(const l in s){const T=s[l].deref();T!==void 0&&d(T)}}return{get:c,update:m,getWireframeAttribute:f,dispose:g}}function jf(i,e,t){let n;function s(x){n=x}let r,a;function o(x){r=x.type,a=x.bytesPerElement}function d(x,f){i.drawElements(n,f,r,x*a),t.update(f,n,1)}function c(x,f,g){g!==0&&(i.drawElementsInstanced(n,f,r,x*a,g),t.update(f,n,g))}function m(x,f,g){if(g===0)return;e.get("WEBGL_multi_draw").multiDrawElementsWEBGL(n,f,0,r,x,0,g);let T=0;for(let p=0;p<g;p++)T+=f[p];t.update(T,n,1)}this.setMode=s,this.setIndex=o,this.render=d,this.renderInstances=c,this.renderMultiDraw=m}function eh(i){const e={geometries:0,textures:0},t={frame:0,calls:0,triangles:0,points:0,lines:0};function n(r,a,o){switch(t.calls++,a){case i.TRIANGLES:t.triangles+=o*(r/3);break;case i.LINES:t.lines+=o*(r/2);break;case i.LINE_STRIP:t.lines+=o*(r-1);break;case i.LINE_LOOP:t.lines+=o*r;break;case i.POINTS:t.points+=o*r;break;default:nt("WebGLInfo: Unknown draw mode:",a);break}}function s(){t.calls=0,t.triangles=0,t.points=0,t.lines=0}return{memory:e,render:t,programs:null,autoReset:!0,reset:s,update:n}}function th(i,e,t){const n=new WeakMap,s=new pt;function r(a,o,d){const c=a.morphTargetInfluences,m=o.morphAttributes.position||o.morphAttributes.normal||o.morphAttributes.color,x=m!==void 0?m.length:0;let f=n.get(o);if(f===void 0||f.count!==x){let I=function(){h.dispose(),n.delete(o),o.removeEventListener("dispose",I)};f!==void 0&&f.texture.dispose();const g=o.morphAttributes.position!==void 0,l=o.morphAttributes.normal!==void 0,T=o.morphAttributes.color!==void 0,p=o.morphAttributes.position||[],u=o.morphAttributes.normal||[],b=o.morphAttributes.color||[];let y=0;g===!0&&(y=1),l===!0&&(y=2),T===!0&&(y=3);let E=o.attributes.position.count*y,R=1;E>e.maxTextureSize&&(R=Math.ceil(E/e.maxTextureSize),E=e.maxTextureSize);const A=new Float32Array(E*R*4*x),h=new $o(A,E,R,x);h.type=hn,h.needsUpdate=!0;const M=y*4;for(let C=0;C<x;C++){const U=p[C],F=u[C],X=b[C],L=E*R*4*C;for(let W=0;W<U.count;W++){const K=W*M;g===!0&&(s.fromBufferAttribute(U,W),A[L+K+0]=s.x,A[L+K+1]=s.y,A[L+K+2]=s.z,A[L+K+3]=0),l===!0&&(s.fromBufferAttribute(F,W),A[L+K+4]=s.x,A[L+K+5]=s.y,A[L+K+6]=s.z,A[L+K+7]=0),T===!0&&(s.fromBufferAttribute(X,W),A[L+K+8]=s.x,A[L+K+9]=s.y,A[L+K+10]=s.z,A[L+K+11]=X.itemSize===4?s.w:1)}}f={count:x,texture:h,size:new Xe(E,R)},n.set(o,f),o.addEventListener("dispose",I)}if(a.isInstancedMesh===!0&&a.morphTexture!==null)d.getUniforms().setValue(i,"morphTexture",a.morphTexture,t);else{let g=0;for(let T=0;T<c.length;T++)g+=c[T];const l=o.morphTargetsRelative?1:1-g;d.getUniforms().setValue(i,"morphTargetBaseInfluence",l),d.getUniforms().setValue(i,"morphTargetInfluences",c)}d.getUniforms().setValue(i,"morphTargetsTexture",f.texture,t),d.getUniforms().setValue(i,"morphTargetsTextureSize",f.size)}return{update:r}}function nh(i,e,t,n,s){let r=new WeakMap;function a(c){const m=s.render.frame,x=c.geometry,f=e.get(c,x);if(r.get(f)!==m&&(e.update(f),r.set(f,m)),c.isInstancedMesh&&(c.hasEventListener("dispose",d)===!1&&c.addEventListener("dispose",d),r.get(c)!==m&&(t.update(c.instanceMatrix,i.ARRAY_BUFFER),c.instanceColor!==null&&t.update(c.instanceColor,i.ARRAY_BUFFER),r.set(c,m))),c.isSkinnedMesh){const g=c.skeleton;r.get(g)!==m&&(g.update(),r.set(g,m))}return f}function o(){r=new WeakMap}function d(c){const m=c.target;m.removeEventListener("dispose",d),n.releaseStatesOfObject(m),t.remove(m.instanceMatrix),m.instanceColor!==null&&t.remove(m.instanceColor)}return{update:a,dispose:o}}const ih={[Do]:"LINEAR_TONE_MAPPING",[Io]:"REINHARD_TONE_MAPPING",[No]:"CINEON_TONE_MAPPING",[jr]:"ACES_FILMIC_TONE_MAPPING",[Fo]:"AGX_TONE_MAPPING",[Oo]:"NEUTRAL_TONE_MAPPING",[Uo]:"CUSTOM_TONE_MAPPING"};function sh(i,e,t,n,s,r){const a=new mn(e,t,{type:i,depthBuffer:s,stencilBuffer:r,samples:n?4:0,storeMultisampledDepthBuffer:!1,storeMultisampledStencilBuffer:!1,resolveDepthBuffer:!1,resolveStencilBuffer:!1});let o=null,d=null,c=null;const m=new cn;m.setAttribute("position",new _t([-1,3,0,-1,-1,0,3,-1,0],3)),m.setAttribute("uv",new _t([0,2,0,0,2,0],2));const x=new Xc({uniforms:{tDiffuse:{value:null}},vertexShader:`
			precision highp float;

			uniform mat4 modelViewMatrix;
			uniform mat4 projectionMatrix;

			attribute vec3 position;
			attribute vec2 uv;

			varying vec2 vUv;

			void main() {
				vUv = uv;
				gl_Position = projectionMatrix * modelViewMatrix * vec4( position, 1.0 );
			}`,fragmentShader:`
			precision highp float;

			uniform sampler2D tDiffuse;

			varying vec2 vUv;

			#include <tonemapping_pars_fragment>
			#include <colorspace_pars_fragment>

			void main() {
				gl_FragColor = texture2D( tDiffuse, vUv );

				#ifdef LINEAR_TONE_MAPPING
					gl_FragColor.rgb = LinearToneMapping( gl_FragColor.rgb );
				#elif defined( REINHARD_TONE_MAPPING )
					gl_FragColor.rgb = ReinhardToneMapping( gl_FragColor.rgb );
				#elif defined( CINEON_TONE_MAPPING )
					gl_FragColor.rgb = CineonToneMapping( gl_FragColor.rgb );
				#elif defined( ACES_FILMIC_TONE_MAPPING )
					gl_FragColor.rgb = ACESFilmicToneMapping( gl_FragColor.rgb );
				#elif defined( AGX_TONE_MAPPING )
					gl_FragColor.rgb = AgXToneMapping( gl_FragColor.rgb );
				#elif defined( NEUTRAL_TONE_MAPPING )
					gl_FragColor.rgb = NeutralToneMapping( gl_FragColor.rgb );
				#elif defined( CUSTOM_TONE_MAPPING )
					gl_FragColor.rgb = CustomToneMapping( gl_FragColor.rgb );
				#endif

				#ifdef SRGB_TRANSFER
					gl_FragColor = sRGBTransferOETF( gl_FragColor );
				#endif
			}`,depthTest:!1,depthWrite:!1}),f=new ht(m,x),g=new ol(-1,1,1,-1,0,1);let l=null,T=null,p=!1,u,b=null,y=[],E=!1;this.setSize=function(R,A){a.setSize(R,A),o!==null&&o.setSize(R,A),d!==null&&d.setSize(R,A);for(let h=0;h<y.length;h++){const M=y[h];M.setSize&&M.setSize(R,A)}},this.setEffects=function(R){y=R,E=y.length>0&&y[0].isRenderPass===!0;const A=a.width,h=a.height;y.length>0&&o===null&&(o=new mn(A,h,{type:xn,depthBuffer:!1,stencilBuffer:!1}),d=new mn(A,h,{type:xn,depthBuffer:!1,stencilBuffer:!1}));const M=s===!0&&y.some(I=>I.needsDepthTexture===!0);M!==(a.depthTexture!==null)&&(M===!0&&c===null&&(c=new vi(A,h),r===!0&&(c.format=Bn,c.type=_i)),a.dispose(),a.depthTexture=M?c:null,a.resolveDepthBuffer=M,a.storeMultisampledDepthBuffer=M);for(let I=0;I<y.length;I++){const C=y[I];C.setSize&&C.setSize(A,h),M&&C.needsDepthTexture===!0&&C.setDepthTexture&&C.setDepthTexture(c)}},this.begin=function(R,A){if(p||R.toneMapping===wn&&y.length===0)return!1;if(b=A,A!==null){const h=A.width,M=A.height;(a.width!==h||a.height!==M)&&this.setSize(h,M)}return E===!1&&R.setRenderTarget(a),u=R.toneMapping,R.toneMapping=wn,!0},this.hasRenderPass=function(){return E},this.end=function(R,A){R.toneMapping=u,p=!0;let h=a,M=o;for(let I=0;I<y.length;I++){const C=y[I];C.enabled!==!1&&(C.render(R,M,h,A),C.needsSwap!==!1&&(h=M,M=M===o?d:o))}if(l!==R.outputColorSpace||T!==R.toneMapping){l=R.outputColorSpace,T=R.toneMapping,x.defines={},Ze.getTransfer(l)===at&&(x.defines.SRGB_TRANSFER="");const I=ih[T];I&&(x.defines[I]=""),x.needsUpdate=!0}x.uniforms.tDiffuse.value=h.texture,R.setRenderTarget(b),R.render(f,g),b=null,p=!1},this.isCompositing=function(){return p},this.dispose=function(){a.dispose(),o!==null&&o.dispose(),d!==null&&d.dispose(),m.dispose(),x.dispose()}}const fl=new zt,hl=new vi(1,1),pl=new nl(1),ml=new $o,gl=new Mc,xl=new tl,co=[],uo=[],fo=new Float32Array(16),ho=new Float32Array(9),po=new Float32Array(4);function bi(i,e,t){const n=i[0];if(n<=0||n>0)return i;const s=e*t;let r=co[s];if(r===void 0&&(r=new Float32Array(s),co[s]=r),e!==0){n.toArray(r,0);for(let a=1,o=0;a!==e;++a)o+=t,i[a].toArray(r,o)}return r}function yt(i,e){if(i.length!==e.length)return!1;for(let t=0,n=i.length;t<n;t++)if(i[t]!==e[t])return!1;return!0}function Et(i,e){for(let t=0,n=e.length;t<n;t++)i[t]=e[t]}function Ps(i,e){let t=uo[e];t===void 0&&(t=new Int32Array(e),uo[e]=t);for(let n=0;n!==e;++n)t[n]=i.allocateTextureUnit();return t}function rh(i,e){const t=this.cache;t[0]!==e&&(i.uniform1f(this.addr,e),t[0]=e)}function ah(i,e){const t=this.cache;if(e.x!==void 0)(t[0]!==e.x||t[1]!==e.y)&&(i.uniform2f(this.addr,e.x,e.y),t[0]=e.x,t[1]=e.y);else{if(yt(t,e))return;i.uniform2fv(this.addr,e),Et(t,e)}}function oh(i,e){const t=this.cache;if(e.x!==void 0)(t[0]!==e.x||t[1]!==e.y||t[2]!==e.z)&&(i.uniform3f(this.addr,e.x,e.y,e.z),t[0]=e.x,t[1]=e.y,t[2]=e.z);else if(e.r!==void 0)(t[0]!==e.r||t[1]!==e.g||t[2]!==e.b)&&(i.uniform3f(this.addr,e.r,e.g,e.b),t[0]=e.r,t[1]=e.g,t[2]=e.b);else{if(yt(t,e))return;i.uniform3fv(this.addr,e),Et(t,e)}}function lh(i,e){const t=this.cache;if(e.x!==void 0)(t[0]!==e.x||t[1]!==e.y||t[2]!==e.z||t[3]!==e.w)&&(i.uniform4f(this.addr,e.x,e.y,e.z,e.w),t[0]=e.x,t[1]=e.y,t[2]=e.z,t[3]=e.w);else{if(yt(t,e))return;i.uniform4fv(this.addr,e),Et(t,e)}}function ch(i,e){const t=this.cache,n=e.elements;if(n===void 0){if(yt(t,e))return;i.uniformMatrix2fv(this.addr,!1,e),Et(t,e)}else{if(yt(t,n))return;po.set(n),i.uniformMatrix2fv(this.addr,!1,po),Et(t,n)}}function dh(i,e){const t=this.cache,n=e.elements;if(n===void 0){if(yt(t,e))return;i.uniformMatrix3fv(this.addr,!1,e),Et(t,e)}else{if(yt(t,n))return;ho.set(n),i.uniformMatrix3fv(this.addr,!1,ho),Et(t,n)}}function uh(i,e){const t=this.cache,n=e.elements;if(n===void 0){if(yt(t,e))return;i.uniformMatrix4fv(this.addr,!1,e),Et(t,e)}else{if(yt(t,n))return;fo.set(n),i.uniformMatrix4fv(this.addr,!1,fo),Et(t,n)}}function fh(i,e){const t=this.cache;t[0]!==e&&(i.uniform1i(this.addr,e),t[0]=e)}function hh(i,e){const t=this.cache;if(e.x!==void 0)(t[0]!==e.x||t[1]!==e.y)&&(i.uniform2i(this.addr,e.x,e.y),t[0]=e.x,t[1]=e.y);else{if(yt(t,e))return;i.uniform2iv(this.addr,e),Et(t,e)}}function ph(i,e){const t=this.cache;if(e.x!==void 0)(t[0]!==e.x||t[1]!==e.y||t[2]!==e.z)&&(i.uniform3i(this.addr,e.x,e.y,e.z),t[0]=e.x,t[1]=e.y,t[2]=e.z);else{if(yt(t,e))return;i.uniform3iv(this.addr,e),Et(t,e)}}function mh(i,e){const t=this.cache;if(e.x!==void 0)(t[0]!==e.x||t[1]!==e.y||t[2]!==e.z||t[3]!==e.w)&&(i.uniform4i(this.addr,e.x,e.y,e.z,e.w),t[0]=e.x,t[1]=e.y,t[2]=e.z,t[3]=e.w);else{if(yt(t,e))return;i.uniform4iv(this.addr,e),Et(t,e)}}function gh(i,e){const t=this.cache;t[0]!==e&&(i.uniform1ui(this.addr,e),t[0]=e)}function xh(i,e){const t=this.cache;if(e.x!==void 0)(t[0]!==e.x||t[1]!==e.y)&&(i.uniform2ui(this.addr,e.x,e.y),t[0]=e.x,t[1]=e.y);else{if(yt(t,e))return;i.uniform2uiv(this.addr,e),Et(t,e)}}function _h(i,e){const t=this.cache;if(e.x!==void 0)(t[0]!==e.x||t[1]!==e.y||t[2]!==e.z)&&(i.uniform3ui(this.addr,e.x,e.y,e.z),t[0]=e.x,t[1]=e.y,t[2]=e.z);else{if(yt(t,e))return;i.uniform3uiv(this.addr,e),Et(t,e)}}function vh(i,e){const t=this.cache;if(e.x!==void 0)(t[0]!==e.x||t[1]!==e.y||t[2]!==e.z||t[3]!==e.w)&&(i.uniform4ui(this.addr,e.x,e.y,e.z,e.w),t[0]=e.x,t[1]=e.y,t[2]=e.z,t[3]=e.w);else{if(yt(t,e))return;i.uniform4uiv(this.addr,e),Et(t,e)}}function Ls(i){return i.version===0&&(i.compareFunction=Ko,i.needsUpdate=!0),i}function Sh(i,e,t){const n=this.cache,s=t.allocateTextureUnit();n[0]!==s&&(i.uniform1i(this.addr,s),n[0]=s);let r;this.type===i.SAMPLER_2D_SHADOW?r=Ls(hl):r=fl,t.setTexture2D(e||r,s)}function Mh(i,e,t){const n=this.cache,s=t.allocateTextureUnit();n[0]!==s&&(i.uniform1i(this.addr,s),n[0]=s),t.setTexture3D(e||gl,s)}function bh(i,e,t){const n=this.cache,s=t.allocateTextureUnit();n[0]!==s&&(i.uniform1i(this.addr,s),n[0]=s);let r;this.type===i.SAMPLER_CUBE_SHADOW?r=Ls(pl):r=xl,t.setTextureCube(e||r,s)}function yh(i,e,t){const n=this.cache,s=t.allocateTextureUnit();n[0]!==s&&(i.uniform1i(this.addr,s),n[0]=s),t.setTexture2DArray(e||ml,s)}function Eh(i){switch(i){case 5126:return rh;case 35664:return ah;case 35665:return oh;case 35666:return lh;case 35674:return ch;case 35675:return dh;case 35676:return uh;case 5124:case 35670:return fh;case 35667:case 35671:return hh;case 35668:case 35672:return ph;case 35669:case 35673:return mh;case 5125:return gh;case 36294:return xh;case 36295:return _h;case 36296:return vh;case 35678:case 36198:case 36298:case 36306:case 35682:return Sh;case 35679:case 36299:case 36307:return Mh;case 35680:case 36300:case 36308:case 36293:return bh;case 36289:case 36303:case 36311:case 36292:return yh}}function Th(i,e){i.uniform1fv(this.addr,e)}function wh(i,e){const t=bi(e,this.size,2);i.uniform2fv(this.addr,t)}function Ah(i,e){const t=bi(e,this.size,3);i.uniform3fv(this.addr,t)}function Rh(i,e){const t=bi(e,this.size,4);i.uniform4fv(this.addr,t)}function Ch(i,e){const t=bi(e,this.size,4);i.uniformMatrix2fv(this.addr,!1,t)}function Ph(i,e){const t=bi(e,this.size,9);i.uniformMatrix3fv(this.addr,!1,t)}function Lh(i,e){const t=bi(e,this.size,16);i.uniformMatrix4fv(this.addr,!1,t)}function Dh(i,e){i.uniform1iv(this.addr,e)}function Ih(i,e){i.uniform2iv(this.addr,e)}function Nh(i,e){i.uniform3iv(this.addr,e)}function Uh(i,e){i.uniform4iv(this.addr,e)}function Fh(i,e){i.uniform1uiv(this.addr,e)}function Oh(i,e){i.uniform2uiv(this.addr,e)}function Bh(i,e){i.uniform3uiv(this.addr,e)}function kh(i,e){i.uniform4uiv(this.addr,e)}function zh(i,e,t){const n=this.cache,s=e.length,r=Ps(t,s);yt(n,r)||(i.uniform1iv(this.addr,r),Et(n,r));let a;this.type===i.SAMPLER_2D_SHADOW?a=Ls(hl):a=fl;for(let o=0;o!==s;++o)t.setTexture2D(e[o]||a,r[o])}function Gh(i,e,t){const n=this.cache,s=e.length,r=Ps(t,s);yt(n,r)||(i.uniform1iv(this.addr,r),Et(n,r));for(let a=0;a!==s;++a)t.setTexture3D(e[a]||gl,r[a])}function Hh(i,e,t){const n=this.cache,s=e.length,r=Ps(t,s);yt(n,r)||(i.uniform1iv(this.addr,r),Et(n,r));let a;this.type===i.SAMPLER_CUBE_SHADOW?a=Ls(pl):a=xl;for(let o=0;o!==s;++o)t.setTextureCube(e[o]||a,r[o])}function Vh(i,e,t){const n=this.cache,s=e.length,r=Ps(t,s);yt(n,r)||(i.uniform1iv(this.addr,r),Et(n,r));for(let a=0;a!==s;++a)t.setTexture2DArray(e[a]||ml,r[a])}function Wh(i){switch(i){case 5126:return Th;case 35664:return wh;case 35665:return Ah;case 35666:return Rh;case 35674:return Ch;case 35675:return Ph;case 35676:return Lh;case 5124:case 35670:return Dh;case 35667:case 35671:return Ih;case 35668:case 35672:return Nh;case 35669:case 35673:return Uh;case 5125:return Fh;case 36294:return Oh;case 36295:return Bh;case 36296:return kh;case 35678:case 36198:case 36298:case 36306:case 35682:return zh;case 35679:case 36299:case 36307:return Gh;case 35680:case 36300:case 36308:case 36293:return Hh;case 36289:case 36303:case 36311:case 36292:return Vh}}class Xh{constructor(e,t,n){this.id=e,this.addr=n,this.cache=[],this.type=t.type,this.setValue=Eh(t.type)}}class qh{constructor(e,t,n){this.id=e,this.addr=n,this.cache=[],this.type=t.type,this.size=t.size,this.setValue=Wh(t.type)}}class Yh{constructor(e){this.id=e,this.seq=[],this.map={}}setValue(e,t,n){const s=this.seq;for(let r=0,a=s.length;r!==a;++r){const o=s[r];o.setValue(e,t[o.id],n)}}}const or=/(\w+)(\])?(\[|\.)?/g;function mo(i,e){i.seq.push(e),i.map[e.id]=e}function Kh(i,e,t){const n=i.name,s=n.length;for(or.lastIndex=0;;){const r=or.exec(n),a=or.lastIndex;let o=r[1];const d=r[2]==="]",c=r[3];if(d&&(o=o|0),c===void 0||c==="["&&a+2===s){mo(t,c===void 0?new Xh(o,i,e):new qh(o,i,e));break}else{let x=t.map[o];x===void 0&&(x=new Yh(o),mo(t,x)),t=x}}}class _s{constructor(e,t){this.seq=[],this.map={};const n=e.getProgramParameter(t,e.ACTIVE_UNIFORMS);for(let a=0;a<n;++a){const o=e.getActiveUniform(t,a),d=e.getUniformLocation(t,o.name);Kh(o,d,this)}const s=[],r=[];for(const a of this.seq)a.type===e.SAMPLER_2D_SHADOW||a.type===e.SAMPLER_CUBE_SHADOW||a.type===e.SAMPLER_2D_ARRAY_SHADOW?s.push(a):r.push(a);s.length>0&&(this.seq=s.concat(r))}setValue(e,t,n,s){const r=this.map[t];r!==void 0&&r.setValue(e,n,s)}setOptional(e,t,n){const s=t[n];s!==void 0&&this.setValue(e,n,s)}static upload(e,t,n,s){for(let r=0,a=t.length;r!==a;++r){const o=t[r],d=n[o.id];d.needsUpdate!==!1&&o.setValue(e,d.value,s)}}static seqWithValue(e,t){const n=[];for(let s=0,r=e.length;s!==r;++s){const a=e[s];a.id in t&&n.push(a)}return n}}function go(i,e,t){const n=i.createShader(e);return i.shaderSource(n,t),i.compileShader(n),n}const Zh=37297;let $h=0;function Jh(i,e){const t=i.split(`
`),n=[],s=Math.max(e-6,0),r=Math.min(e+6,t.length);for(let a=s;a<r;a++){const o=a+1;n.push(`${o===e?">":" "} ${o}: ${t[a]}`)}return n.join(`
`)}const xo=new Ne;function Qh(i){Ze._getMatrix(xo,Ze.workingColorSpace,i);const e=`mat3( ${xo.elements.map(t=>t.toFixed(4))} )`;switch(Ze.getTransfer(i)){case bs:return[e,"LinearTransferOETF"];case at:return[e,"sRGBTransferOETF"];default:return De("WebGLProgram: Unsupported color space: ",i),[e,"LinearTransferOETF"]}}function _o(i,e,t){const n=i.getShaderParameter(e,i.COMPILE_STATUS),r=(i.getShaderInfoLog(e)||"").trim();if(n&&r==="")return"";const a=/ERROR: 0:(\d+)/.exec(r);if(a){const o=parseInt(a[1]);return t.toUpperCase()+`

`+r+`

`+Jh(i.getShaderSource(e),o)}else return r}function jh(i,e){const t=Qh(e);return[`vec4 ${i}( vec4 value ) {`,`	return ${t[1]}( vec4( ${t[0]} * value.rgb, value.a ) );`,"}"].join(`
`)}const ep={[Do]:"Linear",[Io]:"Reinhard",[No]:"Cineon",[jr]:"ACESFilmic",[Fo]:"AgX",[Oo]:"Neutral",[Uo]:"Custom"};function tp(i,e){const t=ep[e];return t===void 0?(De("WebGLProgram: Unsupported toneMapping:",e),"vec3 "+i+"( vec3 color ) { return LinearToneMapping( color ); }"):"vec3 "+i+"( vec3 color ) { return "+t+"ToneMapping( color ); }"}const ds=new z;function np(){Ze.getLuminanceCoefficients(ds);const i=ds.x.toFixed(4),e=ds.y.toFixed(4),t=ds.z.toFixed(4);return["float luminance( const in vec3 rgb ) {",`	const vec3 weights = vec3( ${i}, ${e}, ${t} );`,"	return dot( weights, rgb );","}"].join(`
`)}function ip(i){return[i.extensionClipCullDistance?"#extension GL_ANGLE_clip_cull_distance : require":"",i.extensionMultiDraw?"#extension GL_ANGLE_multi_draw : require":""].filter(Pi).join(`
`)}function sp(i){const e=[];for(const t in i){const n=i[t];n!==!1&&e.push("#define "+t+" "+n)}return e.join(`
`)}function rp(i,e){const t={},n=i.getProgramParameter(e,i.ACTIVE_ATTRIBUTES);for(let s=0;s<n;s++){const r=i.getActiveAttrib(e,s),a=r.name;let o=1;r.type===i.FLOAT_MAT2&&(o=2),r.type===i.FLOAT_MAT3&&(o=3),r.type===i.FLOAT_MAT4&&(o=4),t[a]={type:r.type,location:i.getAttribLocation(e,a),locationSize:o}}return t}function Pi(i){return i!==""}function vo(i,e){const t=e.numSpotLightShadows+e.numSpotLightMaps-e.numSpotLightShadowsWithMaps;return i.replace(/NUM_SUN_LIGHTS/g,e.numSunLights).replace(/NUM_DIR_LIGHTS/g,e.numDirLights).replace(/NUM_SPOT_LIGHTS/g,e.numSpotLights).replace(/NUM_SPOT_LIGHT_MAPS/g,e.numSpotLightMaps).replace(/NUM_SPOT_LIGHT_COORDS/g,t).replace(/NUM_RECT_AREA_LIGHTS/g,e.numRectAreaLights).replace(/NUM_POINT_LIGHTS/g,e.numPointLights).replace(/NUM_HEMI_LIGHTS/g,e.numHemiLights).replace(/NUM_SUN_LIGHT_SHADOWS/g,e.numSunLightShadows).replace(/NUM_DIR_LIGHT_SHADOWS/g,e.numDirLightShadows).replace(/NUM_SPOT_LIGHT_SHADOWS_WITH_MAPS/g,e.numSpotLightShadowsWithMaps).replace(/NUM_SPOT_LIGHT_SHADOWS/g,e.numSpotLightShadows).replace(/NUM_POINT_LIGHT_SHADOWS/g,e.numPointLightShadows).replace(/NUM_LIGHT_PROBE_GRIDS/g,e.numLightProbeGrids)}function So(i,e){return i.replace(/NUM_CLIPPING_PLANES/g,e.numClippingPlanes).replace(/UNION_CLIPPING_PLANES/g,e.numClippingPlanes-e.numClipIntersection)}const ap=/^[ \t]*#include +<([\w\d./]+)>/gm;function Jr(i){return i.replace(ap,op)}function op(i,e){const t=Ve[e];if(t===void 0)throw new Error("THREE.WebGLProgram: Can not resolve #include <"+e+">");return Jr(t)}const lp=/#pragma unroll_loop_start\s+for\s*\(\s*int\s+i\s*=\s*(\d+)\s*;\s*i\s*<\s*(\d+)\s*;\s*i\s*\+\+\s*\)\s*{([\s\S]+?)}\s+#pragma unroll_loop_end/g;function Mo(i){return i.replace(lp,cp)}function cp(i,e,t,n){let s="";for(let r=parseInt(e);r<parseInt(t);r++)s+=n.replace(/\[\s*i\s*\]/g,"[ "+r+" ]").replace(/UNROLLED_LOOP_INDEX/g,r);return s}function bo(i){let e=`precision ${i.precision} float;
	precision ${i.precision} int;
	precision ${i.precision} sampler2D;
	precision ${i.precision} samplerCube;
	precision ${i.precision} sampler3D;
	precision ${i.precision} sampler2DArray;
	precision ${i.precision} sampler2DShadow;
	precision ${i.precision} samplerCubeShadow;
	precision ${i.precision} sampler2DArrayShadow;
	precision ${i.precision} isampler2D;
	precision ${i.precision} isampler3D;
	precision ${i.precision} isamplerCube;
	precision ${i.precision} isampler2DArray;
	precision ${i.precision} usampler2D;
	precision ${i.precision} usampler3D;
	precision ${i.precision} usamplerCube;
	precision ${i.precision} usampler2DArray;
	`;return i.precision==="highp"?e+=`
#define HIGH_PRECISION`:i.precision==="mediump"?e+=`
#define MEDIUM_PRECISION`:i.precision==="lowp"&&(e+=`
#define LOW_PRECISION`),e}const dp={[fs]:"SHADOWMAP_TYPE_PCF",[Ci]:"SHADOWMAP_TYPE_VSM"};function up(i){return dp[i.shadowMapType]||"SHADOWMAP_TYPE_BASIC"}const fp={[Ii]:"ENVMAP_MODE_REFRACTION"};function hp(i){return i.envMap===!1?"ENVMAP_MODE_REFLECTION":fp[i.envMapMode]||"ENVMAP_MODE_REFLECTION"}const pp={[Lo]:"ENVMAP_BLENDING_MULTIPLY",[$l]:"ENVMAP_BLENDING_MIX",[Jl]:"ENVMAP_BLENDING_ADD"};function mp(i){return i.envMap===!1?"ENVMAP_BLENDING_NONE":pp[i.combine]||"ENVMAP_BLENDING_NONE"}function gp(i,e,t,n){const s=i.getContext(),r=t.defines;let a=t.vertexShader,o=t.fragmentShader;const d=up(t),c=t.envMapPMREM===!0?"ENVMAP_TYPE_PMREM":"ENVMAP_TYPE_CUBE",m=hp(t),x=mp(t),f=ip(t),g=sp(r),l=s.createProgram();let T,p,u=t.glslVersion?"#version "+t.glslVersion+`
`:"";t.isRawShaderMaterial?(T=["#define SHADER_TYPE "+t.shaderType,"#define SHADER_NAME "+t.shaderName,g].filter(Pi).join(`
`),T.length>0&&(T+=`
`),p=["#define SHADER_TYPE "+t.shaderType,"#define SHADER_NAME "+t.shaderName,g].filter(Pi).join(`
`),p.length>0&&(p+=`
`)):(T=[bo(t),"#define SHADER_TYPE "+t.shaderType,"#define SHADER_NAME "+t.shaderName,g,t.extensionClipCullDistance?"#define USE_CLIP_DISTANCE":"",t.batching?"#define USE_BATCHING":"",t.batchingColor?"#define USE_BATCHING_COLOR":"",t.instancing?"#define USE_INSTANCING":"",t.instancingColor?"#define USE_INSTANCING_COLOR":"",t.instancingMorph?"#define USE_INSTANCING_MORPH":"",t.useFog&&t.fog?"#define USE_FOG":"",t.useFog&&t.fogExp2?"#define FOG_EXP2":"",t.map?"#define USE_MAP":"",t.envMap?"#define USE_ENVMAP":"",t.envMap?"#define "+m:"",t.lightMap?"#define USE_LIGHTMAP":"",t.aoMap?"#define USE_AOMAP":"",t.bumpMap?"#define USE_BUMPMAP":"",t.normalMap?"#define USE_NORMALMAP":"",t.normalMapObjectSpace?"#define USE_NORMALMAP_OBJECTSPACE":"",t.normalMapTangentSpace?"#define USE_NORMALMAP_TANGENTSPACE":"",t.displacementMap?"#define USE_DISPLACEMENTMAP":"",t.emissiveMap?"#define USE_EMISSIVEMAP":"",t.anisotropy?"#define USE_ANISOTROPY":"",t.anisotropyMap?"#define USE_ANISOTROPYMAP":"",t.clearcoatMap?"#define USE_CLEARCOATMAP":"",t.clearcoatRoughnessMap?"#define USE_CLEARCOAT_ROUGHNESSMAP":"",t.clearcoatNormalMap?"#define USE_CLEARCOAT_NORMALMAP":"",t.diffuseRoughnessMap?"#define USE_DIFFUSE_ROUGHNESSMAP":"",t.iridescenceMap?"#define USE_IRIDESCENCEMAP":"",t.iridescenceThicknessMap?"#define USE_IRIDESCENCE_THICKNESSMAP":"",t.specularMap?"#define USE_SPECULARMAP":"",t.specularColorMap?"#define USE_SPECULAR_COLORMAP":"",t.specularIntensityMap?"#define USE_SPECULAR_INTENSITYMAP":"",t.roughnessMap?"#define USE_ROUGHNESSMAP":"",t.metalnessMap?"#define USE_METALNESSMAP":"",t.alphaMap?"#define USE_ALPHAMAP":"",t.alphaHash?"#define USE_ALPHAHASH":"",t.transmission?"#define USE_TRANSMISSION":"",t.transmissionMap?"#define USE_TRANSMISSIONMAP":"",t.thicknessMap?"#define USE_THICKNESSMAP":"",t.sheenColorMap?"#define USE_SHEEN_COLORMAP":"",t.sheenRoughnessMap?"#define USE_SHEEN_ROUGHNESSMAP":"",t.mapUv?"#define MAP_UV "+t.mapUv:"",t.alphaMapUv?"#define ALPHAMAP_UV "+t.alphaMapUv:"",t.lightMapUv?"#define LIGHTMAP_UV "+t.lightMapUv:"",t.aoMapUv?"#define AOMAP_UV "+t.aoMapUv:"",t.emissiveMapUv?"#define EMISSIVEMAP_UV "+t.emissiveMapUv:"",t.bumpMapUv?"#define BUMPMAP_UV "+t.bumpMapUv:"",t.normalMapUv?"#define NORMALMAP_UV "+t.normalMapUv:"",t.displacementMapUv?"#define DISPLACEMENTMAP_UV "+t.displacementMapUv:"",t.metalnessMapUv?"#define METALNESSMAP_UV "+t.metalnessMapUv:"",t.roughnessMapUv?"#define ROUGHNESSMAP_UV "+t.roughnessMapUv:"",t.anisotropyMapUv?"#define ANISOTROPYMAP_UV "+t.anisotropyMapUv:"",t.clearcoatMapUv?"#define CLEARCOATMAP_UV "+t.clearcoatMapUv:"",t.clearcoatNormalMapUv?"#define CLEARCOAT_NORMALMAP_UV "+t.clearcoatNormalMapUv:"",t.clearcoatRoughnessMapUv?"#define CLEARCOAT_ROUGHNESSMAP_UV "+t.clearcoatRoughnessMapUv:"",t.diffuseRoughnessMapUv?"#define DIFFUSE_ROUGHNESSMAP_UV "+t.diffuseRoughnessMapUv:"",t.iridescenceMapUv?"#define IRIDESCENCEMAP_UV "+t.iridescenceMapUv:"",t.iridescenceThicknessMapUv?"#define IRIDESCENCE_THICKNESSMAP_UV "+t.iridescenceThicknessMapUv:"",t.sheenColorMapUv?"#define SHEEN_COLORMAP_UV "+t.sheenColorMapUv:"",t.sheenRoughnessMapUv?"#define SHEEN_ROUGHNESSMAP_UV "+t.sheenRoughnessMapUv:"",t.specularMapUv?"#define SPECULARMAP_UV "+t.specularMapUv:"",t.specularColorMapUv?"#define SPECULAR_COLORMAP_UV "+t.specularColorMapUv:"",t.specularIntensityMapUv?"#define SPECULAR_INTENSITYMAP_UV "+t.specularIntensityMapUv:"",t.transmissionMapUv?"#define TRANSMISSIONMAP_UV "+t.transmissionMapUv:"",t.thicknessMapUv?"#define THICKNESSMAP_UV "+t.thicknessMapUv:"",t.vertexTangents&&t.flatShading===!1?"#define USE_TANGENT":"",t.vertexNormals?"#define HAS_NORMAL":"",t.vertexColors?"#define USE_COLOR":"",t.vertexAlphas?"#define USE_COLOR_ALPHA":"",t.vertexUv1s?"#define USE_UV1":"",t.vertexUv2s?"#define USE_UV2":"",t.vertexUv3s?"#define USE_UV3":"",t.pointsUvs?"#define USE_POINTS_UV":"",t.flatShading?"#define FLAT_SHADED":"",t.skinning?"#define USE_SKINNING":"",t.morphTargets?"#define USE_MORPHTARGETS":"",t.morphNormals&&t.flatShading===!1?"#define USE_MORPHNORMALS":"",t.morphColors?"#define USE_MORPHCOLORS":"",t.morphTargetsCount>0?"#define MORPHTARGETS_TEXTURE_STRIDE "+t.morphTextureStride:"",t.morphTargetsCount>0?"#define MORPHTARGETS_COUNT "+t.morphTargetsCount:"",t.doubleSided?"#define DOUBLE_SIDED":"",t.flipSided?"#define FLIP_SIDED":"",t.shadowMapEnabled?"#define USE_SHADOWMAP":"",t.shadowMapEnabled?"#define "+d:"",t.sizeAttenuation?"#define USE_SIZEATTENUATION":"",t.numLightProbes>0?"#define USE_LIGHT_PROBES":"",t.logarithmicDepthBuffer?"#define USE_LOGARITHMIC_DEPTH_BUFFER":"",t.reversedDepthBuffer?"#define USE_REVERSED_DEPTH_BUFFER":"","uniform mat4 modelMatrix;","uniform mat4 modelViewMatrix;","uniform mat4 projectionMatrix;","uniform mat4 viewMatrix;","uniform mat3 normalMatrix;","uniform vec3 cameraPosition;","uniform bool isOrthographic;","#ifdef USE_INSTANCING","	attribute mat4 instanceMatrix;","#endif","#ifdef USE_INSTANCING_COLOR","	attribute vec3 instanceColor;","#endif","#ifdef USE_INSTANCING_MORPH","	uniform sampler2D morphTexture;","#endif","attribute vec3 position;","attribute vec3 normal;","attribute vec2 uv;","#ifdef USE_UV1","	attribute vec2 uv1;","#endif","#ifdef USE_UV2","	attribute vec2 uv2;","#endif","#ifdef USE_UV3","	attribute vec2 uv3;","#endif","#ifdef USE_TANGENT","	attribute vec4 tangent;","#endif","#if defined( USE_COLOR_ALPHA )","	attribute vec4 color;","#elif defined( USE_COLOR )","	attribute vec3 color;","#endif","#ifdef USE_SKINNING","	attribute vec4 skinIndex;","	attribute vec4 skinWeight;","#endif",`
`].filter(Pi).join(`
`),p=[bo(t),"#define SHADER_TYPE "+t.shaderType,"#define SHADER_NAME "+t.shaderName,g,t.useFog&&t.fog?"#define USE_FOG":"",t.useFog&&t.fogExp2?"#define FOG_EXP2":"",t.alphaToCoverage?"#define ALPHA_TO_COVERAGE":"",t.map?"#define USE_MAP":"",t.matcap?"#define USE_MATCAP":"",t.envMap?"#define USE_ENVMAP":"",t.envMap?"#define "+c:"",t.envMap?"#define "+m:"",t.envMap?"#define "+x:"",t.envMapPMREM?"#define ENVMAP_MAX_LOD "+t.envMapMaxLod+".0":"",t.envMapPMREM?"#define ENVMAP_SIZE "+t.envMapSize+".0":"",t.lightMap?"#define USE_LIGHTMAP":"",t.aoMap?"#define USE_AOMAP":"",t.bumpMap?"#define USE_BUMPMAP":"",t.normalMap?"#define USE_NORMALMAP":"",t.normalMapObjectSpace?"#define USE_NORMALMAP_OBJECTSPACE":"",t.normalMapTangentSpace?"#define USE_NORMALMAP_TANGENTSPACE":"",t.packedNormalMap?"#define USE_PACKED_NORMALMAP":"",t.emissiveMap?"#define USE_EMISSIVEMAP":"",t.anisotropy?"#define USE_ANISOTROPY":"",t.anisotropyMap?"#define USE_ANISOTROPYMAP":"",t.clearcoat?"#define USE_CLEARCOAT":"",t.clearcoatMap?"#define USE_CLEARCOATMAP":"",t.clearcoatRoughnessMap?"#define USE_CLEARCOAT_ROUGHNESSMAP":"",t.clearcoatNormalMap?"#define USE_CLEARCOAT_NORMALMAP":"",t.diffuseRoughness?"#define USE_DIFFUSE_ROUGHNESS":"",t.diffuseRoughnessMap?"#define USE_DIFFUSE_ROUGHNESSMAP":"",t.dispersion?"#define USE_DISPERSION":"",t.retroreflection?"#define USE_RETROREFLECTION":"",t.iridescence?"#define USE_IRIDESCENCE":"",t.iridescenceMap?"#define USE_IRIDESCENCEMAP":"",t.iridescenceThicknessMap?"#define USE_IRIDESCENCE_THICKNESSMAP":"",t.specularMap?"#define USE_SPECULARMAP":"",t.specularColorMap?"#define USE_SPECULAR_COLORMAP":"",t.specularIntensityMap?"#define USE_SPECULAR_INTENSITYMAP":"",t.roughnessMap?"#define USE_ROUGHNESSMAP":"",t.metalnessMap?"#define USE_METALNESSMAP":"",t.alphaMap?"#define USE_ALPHAMAP":"",t.alphaTest?"#define USE_ALPHATEST":"",t.alphaHash?"#define USE_ALPHAHASH":"",t.sheen?"#define USE_SHEEN":"",t.sheenColorMap?"#define USE_SHEEN_COLORMAP":"",t.sheenRoughnessMap?"#define USE_SHEEN_ROUGHNESSMAP":"",t.transmission?"#define USE_TRANSMISSION":"",t.transmissionMap?"#define USE_TRANSMISSIONMAP":"",t.thicknessMap?"#define USE_THICKNESSMAP":"",t.vertexTangents&&t.flatShading===!1?"#define USE_TANGENT":"",t.vertexColors||t.instancingColor?"#define USE_COLOR":"",t.vertexAlphas||t.batchingColor?"#define USE_COLOR_ALPHA":"",t.vertexUv1s?"#define USE_UV1":"",t.vertexUv2s?"#define USE_UV2":"",t.vertexUv3s?"#define USE_UV3":"",t.pointsUvs?"#define USE_POINTS_UV":"",t.gradientMap?"#define USE_GRADIENTMAP":"",t.flatShading?"#define FLAT_SHADED":"",t.doubleSided?"#define DOUBLE_SIDED":"",t.flipSided?"#define FLIP_SIDED":"",t.shadowMapEnabled?"#define USE_SHADOWMAP":"",t.shadowMapEnabled?"#define "+d:"",t.premultipliedAlpha?"#define PREMULTIPLIED_ALPHA":"",t.numLightProbes>0?"#define USE_LIGHT_PROBES":"",t.decodeVideoTexture?"#define DECODE_VIDEO_TEXTURE":"",t.decodeVideoTextureEmissive?"#define DECODE_VIDEO_TEXTURE_EMISSIVE":"",t.logarithmicDepthBuffer?"#define USE_LOGARITHMIC_DEPTH_BUFFER":"",t.reversedDepthBuffer?"#define USE_REVERSED_DEPTH_BUFFER":"","uniform mat4 viewMatrix;","uniform vec3 cameraPosition;","uniform bool isOrthographic;",t.toneMapping!==wn?"#define TONE_MAPPING":"",t.toneMapping!==wn?Ve.tonemapping_pars_fragment:"",t.toneMapping!==wn?tp("toneMapping",t.toneMapping):"",t.dithering?"#define DITHERING":"",t.opaque?"#define OPAQUE":"",Ve.colorspace_pars_fragment,jh("linearToOutputTexel",t.outputColorSpace),np(),t.useDepthPacking?"#define DEPTH_PACKING "+t.depthPacking:"",`
`].filter(Pi).join(`
`)),a=Jr(a),a=vo(a,t),a=So(a,t),o=Jr(o),o=vo(o,t),o=So(o,t),a=Mo(a),o=Mo(o),t.isRawShaderMaterial!==!0&&(u=`#version 300 es
`,T=[f,"#define attribute in","#define varying out","#define texture2D texture"].join(`
`)+`
`+T,p=["#define varying in",t.glslVersion===Na?"":"layout(location = 0) out highp vec4 pc_fragColor;",t.glslVersion===Na?"":"#define gl_FragColor pc_fragColor","#define gl_FragDepthEXT gl_FragDepth","#define texture2D texture","#define textureCube texture","#define texture2DProj textureProj","#define texture2DLodEXT textureLod","#define texture2DProjLodEXT textureProjLod","#define textureCubeLodEXT textureLod","#define texture2DGradEXT textureGrad","#define texture2DProjGradEXT textureProjGrad","#define textureCubeGradEXT textureGrad"].join(`
`)+`
`+p);const b=u+T+a,y=u+p+o,E=go(s,s.VERTEX_SHADER,b),R=go(s,s.FRAGMENT_SHADER,y);s.attachShader(l,E),s.attachShader(l,R),t.index0AttributeName!==void 0?s.bindAttribLocation(l,0,t.index0AttributeName):t.hasPositionAttribute===!0&&s.bindAttribLocation(l,0,"position"),s.linkProgram(l);function A(C){if(i.debug.checkShaderErrors){const U=s.getProgramInfoLog(l)||"",F=s.getShaderInfoLog(E)||"",X=s.getShaderInfoLog(R)||"",L=U.trim(),W=F.trim(),K=X.trim();let H=!0,ne=!0;if(s.getProgramParameter(l,s.LINK_STATUS)===!1)if(H=!1,typeof i.debug.onShaderError=="function")i.debug.onShaderError(s,l,E,R);else{const q=_o(s,E,"vertex"),ee=_o(s,R,"fragment");nt("WebGLProgram: Shader Error "+s.getError()+" - VALIDATE_STATUS "+s.getProgramParameter(l,s.VALIDATE_STATUS)+`

Material Name: `+C.name+`
Material Type: `+C.type+`

Program Info Log: `+L+`
`+q+`
`+ee)}else L!==""?De("WebGLProgram: Program Info Log:",L):(W===""||K==="")&&(ne=!1);ne&&(C.diagnostics={runnable:H,programLog:L,vertexShader:{log:W,prefix:T},fragmentShader:{log:K,prefix:p}})}s.deleteShader(E),s.deleteShader(R),h=new _s(s,l),M=rp(s,l)}let h;this.getUniforms=function(){return h===void 0&&A(this),h};let M;this.getAttributes=function(){return M===void 0&&A(this),M};let I=t.rendererExtensionParallelShaderCompile===!1;return this.isReady=function(){return I===!1&&(I=s.getProgramParameter(l,Zh)),I},this.destroy=function(){n.releaseStatesOfProgram(this),s.deleteProgram(l),this.program=void 0},this.type=t.shaderType,this.name=t.shaderName,this.id=$h++,this.cacheKey=e,this.usedTimes=1,this.program=l,this.vertexShader=E,this.fragmentShader=R,this}let xp=0;class _p{constructor(){this.shaderCache=new Map,this.materialCache=new Map}update(e,t,n){const s=this._getShaderCacheForMaterial(e);return s.has(t)===!1&&(s.add(t),t.usedTimes++),s.has(n)===!1&&(s.add(n),n.usedTimes++),this}remove(e){const t=this.materialCache.get(e);for(const n of t)n.usedTimes--,n.usedTimes===0&&this.shaderCache.delete(n.code);return this.materialCache.delete(e),this}getVertexShaderStage(e){return this._getShaderStage(e.vertexShader)}getFragmentShaderStage(e){return this._getShaderStage(e.fragmentShader)}dispose(){this.shaderCache.clear(),this.materialCache.clear()}_getShaderCacheForMaterial(e){const t=this.materialCache;let n=t.get(e);return n===void 0&&(n=new Set,t.set(e,n)),n}_getShaderStage(e){const t=this.shaderCache;let n=t.get(e);return n===void 0&&(n=new vp(e),t.set(e,n)),n}}class vp{constructor(e){this.id=xp++,this.code=e,this.usedTimes=0}}function Sp(i){return i===Qn||i===vs||i===Ss}function Mp(i,e,t,n,s,r){const a=new Jo,o=new _p,d=new Set,c=[],m=new Map,x=n.logarithmicDepthBuffer,f={MeshDepthMaterial:"depth",MeshDistanceMaterial:"distance",MeshNormalMaterial:"normal",MeshBasicMaterial:"basic",MeshLambertMaterial:"lambert",MeshPhongMaterial:"phong",MeshToonMaterial:"toon",MeshStandardMaterial:"physical",MeshPhysicalMaterial:"physical",MeshMatcapMaterial:"matcap",LineBasicMaterial:"basic",LineDashedMaterial:"dashed",PointsMaterial:"points",ShadowMaterial:"shadow",SpriteMaterial:"sprite"};function g(h){return d.add(h),h===0?"uv":`uv${h}`}function l(h,M,I,C,U,F){const X=C.fog,L=U.geometry,W=h.isMeshStandardMaterial||h.isMeshLambertMaterial||h.isMeshPhongMaterial?C.environment:null,K=h.isMeshStandardMaterial||h.isMeshLambertMaterial&&!h.envMap||h.isMeshPhongMaterial&&!h.envMap,H=e.get(h.envMap||W,K),ne=f[h.type];let q=n.precision;h.precision!==null&&(q=n.getMaxPrecision(h.precision),q!==h.precision&&De("WebGLProgram.getParameters:",h.precision,"not supported, using",q,"instead."));const ee=L.morphAttributes.position||L.morphAttributes.normal||L.morphAttributes.color,te=ee!==void 0?ee.length:0;let Me=0;L.morphAttributes.position!==void 0&&(Me=1),L.morphAttributes.normal!==void 0&&(Me=2),L.morphAttributes.color!==void 0&&(Me=3);let Le,$,pe,ve;if(ne){const ge=fn[ne];Le=ge.vertexShader,$=ge.fragmentShader}else{Le=h.vertexShader,$=h.fragmentShader;const ge=o.getVertexShaderStage(h),rt=o.getFragmentShaderStage(h);o.update(h,ge,rt),pe=ge.id,ve=rt.id}const Ae=i.getRenderTarget(),me=i.state.buffers.depth.getReversed(),Ie=U.isInstancedMesh===!0,gt=U.isBatchedMesh===!0,Re=!!h.map,et=!!h.matcap,it=!!H,Ge=it&&H.isPMREMTexture===!0,bt=!!h.aoMap,xt=!!h.lightMap,Pt=!!h.bumpMap&&h.wireframe===!1,ct=!!h.normalMap,qt=!!h.displacementMap,vt=!!h.emissiveMap,Ft=!!h.metalnessMap,Ot=!!h.roughnessMap,Gt=h.anisotropy>0,N=h.clearcoat>0,Ht=h.diffuseRoughness>0,ot=h.dispersion>0,dt=h.retroreflectivity>0,le=h.iridescence>0,st=h.sheen>0,Ce=h.transmission>0,Pe=Gt&&!!h.anisotropyMap,w=N&&!!h.clearcoatMap,v=N&&!!h.clearcoatNormalMap,B=N&&!!h.clearcoatRoughnessMap,ie=Ht&&!!h.diffuseRoughnessMap,j=le&&!!h.iridescenceMap,ae=le&&!!h.iridescenceThicknessMap,fe=st&&!!h.sheenColorMap,Z=st&&!!h.sheenRoughnessMap,J=!!h.specularMap,ce=!!h.specularColorMap,P=!!h.specularIntensityMap,Q=Ce&&!!h.transmissionMap,Y=Ce&&!!h.thicknessMap,de=!!h.gradientMap,re=!!h.alphaMap,oe=h.alphaTest>0,Te=!!h.alphaHash,He=!!h.extensions;let se=wn;h.toneMapped&&(Ae===null||Ae.isXRRenderTarget===!0)&&(se=i.toneMapping);const we=Ge?H.isCompressedCubeTexture?H.image[0].mipmaps:H.mipmaps:null,ze={shaderID:ne,shaderType:h.type,shaderName:h.name,vertexShader:Le,fragmentShader:$,defines:h.defines,customVertexShaderID:pe,customFragmentShaderID:ve,isRawShaderMaterial:h.isRawShaderMaterial===!0,glslVersion:h.glslVersion,precision:q,batching:gt,batchingColor:gt&&U._colorsTexture!==null,instancing:Ie,instancingColor:Ie&&U.instanceColor!==null,instancingMorph:Ie&&U.morphTexture!==null,outputColorSpace:Ae===null?i.outputColorSpace:Ae.isXRRenderTarget===!0?Ae.texture.colorSpace:Ze.workingColorSpace,alphaToCoverage:!!h.alphaToCoverage,map:Re,matcap:et,envMap:it,envMapMode:it&&H.mapping,envMapPMREM:Ge,envMapMaxLod:Ge?we.length-1:null,envMapSize:Ge?we[0].width:null,aoMap:bt,lightMap:xt,bumpMap:Pt,normalMap:ct,displacementMap:qt,emissiveMap:vt,normalMapObjectSpace:ct&&h.normalMapType===ec,normalMapTangentSpace:ct&&h.normalMapType===Kr,packedNormalMap:ct&&h.normalMapType===Kr&&Sp(h.normalMap.format),metalnessMap:Ft,roughnessMap:Ot,anisotropy:Gt,anisotropyMap:Pe,clearcoat:N,clearcoatMap:w,clearcoatNormalMap:v,clearcoatRoughnessMap:B,diffuseRoughness:Ht,diffuseRoughnessMap:ie,dispersion:ot,retroreflection:dt,iridescence:le,iridescenceMap:j,iridescenceThicknessMap:ae,sheen:st,sheenColorMap:fe,sheenRoughnessMap:Z,specularMap:J,specularColorMap:ce,specularIntensityMap:P,transmission:Ce,transmissionMap:Q,thicknessMap:Y,gradientMap:de,opaque:h.transparent===!1&&h.blending===Li&&h.alphaToCoverage===!1,alphaMap:re,alphaTest:oe,alphaHash:Te,combine:h.combine,mapUv:Re&&g(h.map.channel),aoMapUv:bt&&g(h.aoMap.channel),lightMapUv:xt&&g(h.lightMap.channel),bumpMapUv:Pt&&g(h.bumpMap.channel),normalMapUv:ct&&g(h.normalMap.channel),displacementMapUv:qt&&g(h.displacementMap.channel),emissiveMapUv:vt&&g(h.emissiveMap.channel),metalnessMapUv:Ft&&g(h.metalnessMap.channel),roughnessMapUv:Ot&&g(h.roughnessMap.channel),anisotropyMapUv:Pe&&g(h.anisotropyMap.channel),clearcoatMapUv:w&&g(h.clearcoatMap.channel),clearcoatNormalMapUv:v&&g(h.clearcoatNormalMap.channel),clearcoatRoughnessMapUv:B&&g(h.clearcoatRoughnessMap.channel),diffuseRoughnessMapUv:ie&&g(h.diffuseRoughnessMap.channel),iridescenceMapUv:j&&g(h.iridescenceMap.channel),iridescenceThicknessMapUv:ae&&g(h.iridescenceThicknessMap.channel),sheenColorMapUv:fe&&g(h.sheenColorMap.channel),sheenRoughnessMapUv:Z&&g(h.sheenRoughnessMap.channel),specularMapUv:J&&g(h.specularMap.channel),specularColorMapUv:ce&&g(h.specularColorMap.channel),specularIntensityMapUv:P&&g(h.specularIntensityMap.channel),transmissionMapUv:Q&&g(h.transmissionMap.channel),thicknessMapUv:Y&&g(h.thicknessMap.channel),alphaMapUv:re&&g(h.alphaMap.channel),vertexTangents:!!L.attributes.tangent&&(ct||Gt),vertexNormals:!!L.attributes.normal,vertexColors:h.vertexColors,vertexAlphas:h.vertexColors===!0&&!!L.attributes.color&&L.attributes.color.itemSize===4,pointsUvs:U.isPoints===!0&&!!L.attributes.uv&&(Re||re),fog:!!X,useFog:h.fog===!0,fogExp2:!!X&&X.isFogExp2,flatShading:h.wireframe===!1&&(h.flatShading===!0||L.attributes.normal===void 0&&ct===!1&&(h.isMeshLambertMaterial||h.isMeshPhongMaterial||h.isMeshStandardMaterial||h.isMeshPhysicalMaterial)),sizeAttenuation:h.sizeAttenuation===!0,logarithmicDepthBuffer:x,reversedDepthBuffer:me,skinning:U.isSkinnedMesh===!0,hasPositionAttribute:L.attributes.position!==void 0,morphTargets:L.morphAttributes.position!==void 0,morphNormals:L.morphAttributes.normal!==void 0,morphColors:L.morphAttributes.color!==void 0,morphTargetsCount:te,morphTextureStride:Me,numSunLights:M.sun.length,numDirLights:M.directional.length,numPointLights:M.point.length,numSpotLights:M.spot.length,numSpotLightMaps:M.spotLightMap.length,numRectAreaLights:M.rectArea.length,numHemiLights:M.hemi.length,numSunLightShadows:M.sunShadowMap.length,numDirLightShadows:M.directionalShadowMap.length,numPointLightShadows:M.pointShadowMap.length,numSpotLightShadows:M.spotShadowMap.length,numSpotLightShadowsWithMaps:M.numSpotLightShadowsWithMaps,numLightProbes:M.numLightProbes,numLightProbeGrids:F.filter(ge=>ge.texture!==null).length,numClippingPlanes:r.numPlanes,numClipIntersection:r.numIntersection,dithering:h.dithering,shadowMapEnabled:i.shadowMap.enabled&&I.length>0,shadowMapType:i.shadowMap.type,toneMapping:se,decodeVideoTexture:Re&&h.map.isVideoTexture===!0&&Ze.getTransfer(h.map.colorSpace)===at,decodeVideoTextureEmissive:vt&&h.emissiveMap.isVideoTexture===!0&&Ze.getTransfer(h.emissiveMap.colorSpace)===at,premultipliedAlpha:h.premultipliedAlpha,doubleSided:h.side===En,flipSided:h.side===Xt,useDepthPacking:h.depthPacking>=0,depthPacking:h.depthPacking||0,index0AttributeName:h.index0AttributeName,extensionClipCullDistance:He&&h.extensions.clipCullDistance===!0&&t.has("WEBGL_clip_cull_distance"),extensionMultiDraw:(He&&h.extensions.multiDraw===!0||gt)&&t.has("WEBGL_multi_draw"),rendererExtensionParallelShaderCompile:t.has("KHR_parallel_shader_compile"),customProgramCacheKey:h.customProgramCacheKey()};return ze.vertexUv1s=d.has(1),ze.vertexUv2s=d.has(2),ze.vertexUv3s=d.has(3),d.clear(),ze}function T(h){const M=[];if(h.shaderID?M.push(h.shaderID):(M.push(h.customVertexShaderID),M.push(h.customFragmentShaderID)),h.defines!==void 0)for(const I in h.defines)M.push(I),M.push(h.defines[I]);return h.isRawShaderMaterial===!1&&(p(M,h),u(M,h),M.push(i.outputColorSpace)),M.push(h.customProgramCacheKey),M.join()}function p(h,M){h.push(M.precision),h.push(M.outputColorSpace),h.push(M.envMapMode),h.push(M.envMapPMREM),h.push(M.envMapMaxLod),h.push(M.envMapSize),h.push(M.mapUv),h.push(M.alphaMapUv),h.push(M.lightMapUv),h.push(M.aoMapUv),h.push(M.bumpMapUv),h.push(M.normalMapUv),h.push(M.displacementMapUv),h.push(M.emissiveMapUv),h.push(M.metalnessMapUv),h.push(M.roughnessMapUv),h.push(M.anisotropyMapUv),h.push(M.clearcoatMapUv),h.push(M.clearcoatNormalMapUv),h.push(M.clearcoatRoughnessMapUv),h.push(M.diffuseRoughnessMapUv),h.push(M.iridescenceMapUv),h.push(M.iridescenceThicknessMapUv),h.push(M.sheenColorMapUv),h.push(M.sheenRoughnessMapUv),h.push(M.specularMapUv),h.push(M.specularColorMapUv),h.push(M.specularIntensityMapUv),h.push(M.transmissionMapUv),h.push(M.thicknessMapUv),h.push(M.combine),h.push(M.fogExp2),h.push(M.sizeAttenuation),h.push(M.morphTargetsCount),h.push(M.numSunLights),h.push(M.numDirLights),h.push(M.numPointLights),h.push(M.numSpotLights),h.push(M.numSpotLightMaps),h.push(M.numHemiLights),h.push(M.numRectAreaLights),h.push(M.numSunLightShadows),h.push(M.numDirLightShadows),h.push(M.numPointLightShadows),h.push(M.numSpotLightShadows),h.push(M.numSpotLightShadowsWithMaps),h.push(M.numLightProbes),h.push(M.numLightProbeGrids),h.push(M.shadowMapType),h.push(M.toneMapping),h.push(M.numClippingPlanes),h.push(M.numClipIntersection),h.push(M.depthPacking)}function u(h,M){a.disableAll(),M.instancing&&a.enable(0),M.instancingColor&&a.enable(1),M.instancingMorph&&a.enable(2),M.matcap&&a.enable(3),M.envMap&&a.enable(4),M.normalMapObjectSpace&&a.enable(5),M.normalMapTangentSpace&&a.enable(6),M.clearcoat&&a.enable(7),M.iridescence&&a.enable(8),M.alphaTest&&a.enable(9),M.vertexColors&&a.enable(10),M.vertexAlphas&&a.enable(11),M.vertexUv1s&&a.enable(12),M.vertexUv2s&&a.enable(13),M.vertexUv3s&&a.enable(14),M.vertexTangents&&a.enable(15),M.anisotropy&&a.enable(16),M.alphaHash&&a.enable(17),M.batching&&a.enable(18),M.dispersion&&a.enable(19),M.retroreflection&&a.enable(24),M.diffuseRoughness&&a.enable(25),M.batchingColor&&a.enable(20),M.gradientMap&&a.enable(21),M.packedNormalMap&&a.enable(22),M.vertexNormals&&a.enable(23),h.push(a.mask),a.disableAll(),M.fog&&a.enable(0),M.useFog&&a.enable(1),M.flatShading&&a.enable(2),M.logarithmicDepthBuffer&&a.enable(3),M.reversedDepthBuffer&&a.enable(4),M.skinning&&a.enable(5),M.morphTargets&&a.enable(6),M.morphNormals&&a.enable(7),M.morphColors&&a.enable(8),M.premultipliedAlpha&&a.enable(9),M.shadowMapEnabled&&a.enable(10),M.doubleSided&&a.enable(11),M.flipSided&&a.enable(12),M.useDepthPacking&&a.enable(13),M.dithering&&a.enable(14),M.transmission&&a.enable(15),M.sheen&&a.enable(16),M.opaque&&a.enable(17),M.pointsUvs&&a.enable(18),M.decodeVideoTexture&&a.enable(19),M.decodeVideoTextureEmissive&&a.enable(20),M.alphaToCoverage&&a.enable(21),M.numLightProbeGrids>0&&a.enable(22),M.hasPositionAttribute&&a.enable(23),h.push(a.mask)}function b(h){const M=f[h.type];let I;if(M){const C=fn[M];I=Hc.clone(C.uniforms)}else I=h.uniforms;return I}function y(h,M){let I=m.get(M);return I!==void 0?++I.usedTimes:(I=new gp(i,M,h,s),c.push(I),m.set(M,I)),I}function E(h){if(--h.usedTimes===0){const M=c.indexOf(h);c[M]=c[c.length-1],c.pop(),m.delete(h.cacheKey),h.destroy()}}function R(h){o.remove(h)}function A(){o.dispose()}return{getParameters:l,getProgramCacheKey:T,getUniforms:b,acquireProgram:y,releaseProgram:E,releaseShaderCache:R,programs:c,dispose:A}}function bp(){let i=new WeakMap;function e(a){return i.has(a)}function t(a){let o=i.get(a);return o===void 0&&(o={},i.set(a,o)),o}function n(a){i.delete(a)}function s(a,o,d){i.get(a)[o]=d}function r(){i=new WeakMap}return{has:e,get:t,remove:n,update:s,dispose:r}}function yp(i,e){return i.groupOrder!==e.groupOrder?i.groupOrder-e.groupOrder:i.renderOrder!==e.renderOrder?i.renderOrder-e.renderOrder:i.material.id!==e.material.id?i.material.id-e.material.id:i.materialVariant!==e.materialVariant?i.materialVariant-e.materialVariant:i.z!==e.z?i.z-e.z:i.id-e.id}function yo(i,e){return i.groupOrder!==e.groupOrder?i.groupOrder-e.groupOrder:i.renderOrder!==e.renderOrder?i.renderOrder-e.renderOrder:i.z!==e.z?e.z-i.z:i.id-e.id}function Eo(){const i=[];let e=0;const t=[],n=[],s=[];function r(){e=0,t.length=0,n.length=0,s.length=0}function a(f){let g=0;return f.isInstancedMesh&&(g+=2),f.isSkinnedMesh&&(g+=1),g}function o(f,g,l,T,p,u){let b=i[e];return b===void 0?(b={id:f.id,object:f,geometry:g,material:l,materialVariant:a(f),groupOrder:T,renderOrder:f.renderOrder,z:p,group:u},i[e]=b):(b.id=f.id,b.object=f,b.geometry=g,b.material=l,b.materialVariant=a(f),b.groupOrder=T,b.renderOrder=f.renderOrder,b.z=p,b.group=u),e++,b}function d(f,g,l,T,p,u,b){b.reversedDepth===!0&&(p=-p);const y=o(f,g,l,T,p,u);l.transmission>0?n.push(y):l.transparent===!0?s.push(y):t.push(y)}function c(f,g,l,T,p,u){const b=o(f,g,l,T,p,u);l.transmission>0?n.unshift(b):l.transparent===!0?s.unshift(b):t.unshift(b)}function m(f,g){t.length>1&&t.sort(f||yp),n.length>1&&n.sort(g||yo),s.length>1&&s.sort(g||yo)}function x(){for(let f=e,g=i.length;f<g;f++){const l=i[f];if(l.id===null)break;l.id=null,l.object=null,l.geometry=null,l.material=null,l.group=null}}return{opaque:t,transmissive:n,transparent:s,init:r,push:d,unshift:c,finish:x,sort:m}}function Ep(){let i=new WeakMap;function e(n,s){const r=i.get(n);let a;return r===void 0?(a=new Eo,i.set(n,[a])):s>=r.length?(a=new Eo,r.push(a)):a=r[s],a}function t(){i=new WeakMap}return{get:e,dispose:t}}function Tp(){const i={};return{get:function(e){if(i[e.id]!==void 0)return i[e.id];let t;switch(e.type){case"SunLight":case"DirectionalLight":t={direction:new z,color:new je};break;case"SpotLight":t={position:new z,direction:new z,color:new je,distance:0,coneCos:0,penumbraCos:0,decay:0};break;case"PointLight":t={position:new z,color:new je,distance:0,decay:0};break;case"HemisphereLight":t={direction:new z,skyColor:new je,groundColor:new je};break;case"RectAreaLight":t={color:new je,position:new z,halfWidth:new z,halfHeight:new z};break}return i[e.id]=t,t}}}function wp(){const i={};return{get:function(e){if(i[e.id]!==void 0)return i[e.id];let t;switch(e.type){case"SunLight":case"DirectionalLight":t={shadowIntensity:1,shadowBias:0,shadowNormalBias:0,shadowRadius:1,shadowMapSize:new Xe};break;case"SpotLight":t={shadowIntensity:1,shadowBias:0,shadowNormalBias:0,shadowRadius:1,shadowMapSize:new Xe};break;case"PointLight":t={shadowIntensity:1,shadowBias:0,shadowNormalBias:0,shadowRadius:1,shadowMapSize:new Xe,shadowCameraNear:1,shadowCameraFar:1e3};break}return i[e.id]=t,t}}}let Ap=0;function Rp(i,e){return(e.castShadow?2:0)-(i.castShadow?2:0)+(e.map?1:0)-(i.map?1:0)}function Cp(i){const e=new Tp,t=wp(),n={version:0,hash:{sunLength:-1,directionalLength:-1,pointLength:-1,spotLength:-1,rectAreaLength:-1,hemiLength:-1,numSunShadows:-1,numDirectionalShadows:-1,numPointShadows:-1,numSpotShadows:-1,numSpotMaps:-1,numLightProbes:-1},ambient:[0,0,0],probe:[],sun:[],sunShadow:[],sunShadowMap:[],sunShadowMatrix:[],sunShadowCascade:[],directional:[],directionalShadow:[],directionalShadowMap:[],directionalShadowMatrix:[],spot:[],spotLightMap:[],spotShadow:[],spotShadowMap:[],spotLightMatrix:[],rectArea:[],rectAreaLTC1:null,rectAreaLTC2:null,point:[],pointShadow:[],pointShadowMap:[],pointShadowMatrix:[],hemi:[],numSpotLightShadowsWithMaps:0,numLightProbes:0};for(let c=0;c<9;c++)n.probe.push(new z);const s=new z,r=new mt,a=new mt;function o(c){let m=0,x=0,f=0;for(let F=0;F<9;F++)n.probe[F].set(0,0,0);let g=0,l=0,T=0,p=0,u=0,b=0,y=0,E=0,R=0,A=0,h=0,M=0,I=0,C=0;c.sort(Rp);for(let F=0,X=c.length;F<X;F++){const L=c[F],W=L.color,K=L.intensity,H=L.distance;let ne=null;if(L.shadow&&L.shadow.map&&(L.shadow.map.texture.format===Qn?ne=L.shadow.map.texture:ne=L.shadow.map.depthTexture||L.shadow.map.texture),L.isAmbientLight)m+=W.r*K,x+=W.g*K,f+=W.b*K;else if(L.isLightProbe){for(let q=0;q<9;q++)n.probe[q].addScaledVector(L.sh.coefficients[q],K);C++}else if(L.isSunLight){const q=e.get(L);if(q.color.copy(L.color).multiplyScalar(L.intensity),L.castShadow){const ee=L.shadow,te=t.get(L);te.shadowIntensity=ee.intensity,te.shadowBias=ee.bias,te.shadowNormalBias=ee.normalBias,te.shadowRadius=ee.radius,te.shadowMapSize.copy(ee.mapSize).multiply(ee.getFrameExtents()),n.sunShadow[l]=te,n.sunShadowMap[l]=ne;const Me=ee.getViewportCount();for(let Le=0;Le<Me;Le++)n.sunShadowMatrix[T+Le]=ee.getMatrix(Le),n.sunShadowCascade[T+Le]=ee._cascadeData[Le];T+=Me,l++}n.sun[g]=q,g++}else if(L.isDirectionalLight){const q=e.get(L);if(q.color.copy(L.color).multiplyScalar(L.intensity),L.castShadow){const ee=L.shadow,te=t.get(L);te.shadowIntensity=ee.intensity,te.shadowBias=ee.bias,te.shadowNormalBias=ee.normalBias,te.shadowRadius=ee.radius,te.shadowMapSize=ee.mapSize,n.directionalShadow[p]=te,n.directionalShadowMap[p]=ne,n.directionalShadowMatrix[p]=L.shadow.matrix,R++}n.directional[p]=q,p++}else if(L.isSpotLight){const q=e.get(L);q.position.setFromMatrixPosition(L.matrixWorld),q.color.copy(W).multiplyScalar(K),q.distance=H,q.coneCos=Math.cos(L.angle),q.penumbraCos=Math.cos(L.angle*(1-L.penumbra)),q.decay=L.decay,n.spot[b]=q;const ee=L.shadow;if(L.map&&(n.spotLightMap[M]=L.map,M++,ee.updateMatrices(L),L.castShadow&&I++),n.spotLightMatrix[b]=ee.matrix,L.castShadow){const te=t.get(L);te.shadowIntensity=ee.intensity,te.shadowBias=ee.bias,te.shadowNormalBias=ee.normalBias,te.shadowRadius=ee.radius,te.shadowMapSize=ee.mapSize,n.spotShadow[b]=te,n.spotShadowMap[b]=ne,h++}b++}else if(L.isRectAreaLight){const q=e.get(L);q.color.copy(W).multiplyScalar(K),q.halfWidth.set(L.width*.5,0,0),q.halfHeight.set(0,L.height*.5,0),n.rectArea[y]=q,y++}else if(L.isPointLight){const q=e.get(L);if(q.color.copy(L.color).multiplyScalar(L.intensity),q.distance=L.distance,q.decay=L.decay,L.castShadow){const ee=L.shadow,te=t.get(L);te.shadowIntensity=ee.intensity,te.shadowBias=ee.bias,te.shadowNormalBias=ee.normalBias,te.shadowRadius=ee.radius,te.shadowMapSize=ee.mapSize,te.shadowCameraNear=ee.camera.near,te.shadowCameraFar=ee.camera.far,n.pointShadow[u]=te,n.pointShadowMap[u]=ne,n.pointShadowMatrix[u]=L.shadow.matrix,A++}n.point[u]=q,u++}else if(L.isHemisphereLight){const q=e.get(L);q.skyColor.copy(L.color).multiplyScalar(K),q.groundColor.copy(L.groundColor).multiplyScalar(K),n.hemi[E]=q,E++}}y>0&&(i.has("OES_texture_float_linear")===!0?(n.rectAreaLTC1=xe.LTC_FLOAT_1,n.rectAreaLTC2=xe.LTC_FLOAT_2):(n.rectAreaLTC1=xe.LTC_HALF_1,n.rectAreaLTC2=xe.LTC_HALF_2)),n.ambient[0]=m,n.ambient[1]=x,n.ambient[2]=f;const U=n.hash;(U.sunLength!==g||U.directionalLength!==p||U.pointLength!==u||U.spotLength!==b||U.rectAreaLength!==y||U.hemiLength!==E||U.numSunShadows!==l||U.numDirectionalShadows!==R||U.numPointShadows!==A||U.numSpotShadows!==h||U.numSpotMaps!==M||U.numLightProbes!==C)&&(n.sun.length=g,n.directional.length=p,n.spot.length=b,n.rectArea.length=y,n.point.length=u,n.hemi.length=E,n.sunShadow.length=l,n.sunShadowMap.length=l,n.sunShadowMatrix.length=T,n.sunShadowCascade.length=T,n.directionalShadow.length=R,n.directionalShadowMap.length=R,n.directionalShadowMatrix.length=R,n.pointShadow.length=A,n.pointShadowMap.length=A,n.pointShadowMatrix.length=A,n.spotShadow.length=h,n.spotShadowMap.length=h,n.spotLightMatrix.length=h+M-I,n.spotLightMap.length=M,n.numSpotLightShadowsWithMaps=I,n.numLightProbes=C,U.sunLength=g,U.directionalLength=p,U.pointLength=u,U.spotLength=b,U.rectAreaLength=y,U.hemiLength=E,U.numSunShadows=l,U.numDirectionalShadows=R,U.numPointShadows=A,U.numSpotShadows=h,U.numSpotMaps=M,U.numLightProbes=C,n.version=Ap++)}function d(c,m){let x=0,f=0,g=0,l=0,T=0,p=0;const u=m.matrixWorldInverse;for(let b=0,y=c.length;b<y;b++){const E=c[b];if(E.isSunLight){const R=n.sun[x];R.direction.setFromMatrixPosition(E.matrixWorld),R.direction.transformDirection(u),x++}else if(E.isDirectionalLight){const R=n.directional[f];R.direction.setFromMatrixPosition(E.matrixWorld),s.setFromMatrixPosition(E.target.matrixWorld),R.direction.sub(s),R.direction.transformDirection(u),f++}else if(E.isSpotLight){const R=n.spot[l];R.position.setFromMatrixPosition(E.matrixWorld),R.position.applyMatrix4(u),R.direction.setFromMatrixPosition(E.matrixWorld),s.setFromMatrixPosition(E.target.matrixWorld),R.direction.sub(s),R.direction.transformDirection(u),l++}else if(E.isRectAreaLight){const R=n.rectArea[T];R.position.setFromMatrixPosition(E.matrixWorld),R.position.applyMatrix4(u),a.identity(),r.copy(E.matrixWorld),r.premultiply(u),a.extractRotation(r),R.halfWidth.set(E.width*.5,0,0),R.halfHeight.set(0,E.height*.5,0),R.halfWidth.applyMatrix4(a),R.halfHeight.applyMatrix4(a),T++}else if(E.isPointLight){const R=n.point[g];R.position.setFromMatrixPosition(E.matrixWorld),R.position.applyMatrix4(u),g++}else if(E.isHemisphereLight){const R=n.hemi[p];R.direction.setFromMatrixPosition(E.matrixWorld),R.direction.transformDirection(u),p++}}}return{setup:o,setupView:d,state:n}}function To(i){const e=new Cp(i),t=[],n=[],s=[];function r(x){m.camera=x,t.length=0,n.length=0,s.length=0}function a(x){x.isLightProbeGrid?s.push(x):t.push(x)}function o(x){n.push(x)}function d(){e.setup(t),s.sort((x,f)=>x.priority-f.priority||x.id-f.id)}function c(x){e.setupView(t,x)}const m={lightsArray:t,shadowsArray:n,lightProbeGridArray:s,camera:null,lights:e,transmissionRenderTarget:{},textureUnits:0};return{init:r,state:m,setupLights:d,setupLightsView:c,pushLight:a,pushShadow:o}}function Pp(i){let e=new WeakMap;function t(s,r=0){const a=e.get(s);let o;return a===void 0?(o=new To(i),e.set(s,[o])):r>=a.length?(o=new To(i),a.push(o)):o=a[r],o}function n(){e=new WeakMap}return{get:t,dispose:n}}const Lp=`void main() {
	gl_Position = vec4( position, 1.0 );
}`,Dp=`uniform sampler2D shadow_pass;
uniform vec2 resolution;
uniform float radius;
void main() {
	const float samples = float( VSM_SAMPLES );
	float mean = 0.0;
	float squared_mean = 0.0;
	float uvStride = samples <= 1.0 ? 0.0 : 2.0 / ( samples - 1.0 );
	float uvStart = samples <= 1.0 ? 0.0 : - 1.0;
	for ( float i = 0.0; i < samples; i ++ ) {
		float uvOffset = uvStart + i * uvStride;
		#ifdef HORIZONTAL_PASS
			vec2 distribution = texture2D( shadow_pass, ( gl_FragCoord.xy + vec2( uvOffset, 0.0 ) * radius ) / resolution ).rg;
			mean += distribution.x;
			squared_mean += distribution.y * distribution.y + distribution.x * distribution.x;
		#else
			float depth = texture2D( shadow_pass, ( gl_FragCoord.xy + vec2( 0.0, uvOffset ) * radius ) / resolution ).r;
			mean += depth;
			squared_mean += depth * depth;
		#endif
	}
	mean = mean / samples;
	squared_mean = squared_mean / samples;
	float std_dev = sqrt( max( 0.0, squared_mean - mean * mean ) );
	gl_FragColor = vec4( mean, std_dev, 0.0, 1.0 );
}`,Ip=[new z(1,0,0),new z(-1,0,0),new z(0,1,0),new z(0,-1,0),new z(0,0,1),new z(0,0,-1)],Np=[new z(0,-1,0),new z(0,-1,0),new z(0,0,1),new z(0,0,-1),new z(0,-1,0),new z(0,-1,0)],wo=new mt,Ri=new z,lr=new z;function Up(i,e,t){let n=new Fi;const s=new Xe,r=new Xe,a=new pt,o=new qc,d=new Yc,c={},m=t.maxTextureSize,x={[Jn]:Xt,[Xt]:Jn,[En]:En},f=new jn({defines:{VSM_SAMPLES:8},uniforms:{shadow_pass:{value:null},resolution:{value:new Xe},radius:{value:4}},vertexShader:Lp,fragmentShader:Dp}),g=f.clone();g.defines.HORIZONTAL_PASS=1;const l=new cn;l.setAttribute("position",new $n(new Float32Array([-1,-1,.5,3,-1,.5,-1,3,.5]),3));const T=new ht(l,f),p=this;this.enabled=!1,this.autoUpdate=!0,this.needsUpdate=!1,this.type=fs;let u=this.type;this.render=function(A,h,M){if(p.enabled===!1||p.autoUpdate===!1&&p.needsUpdate===!1||A.length===0)return;this.type===Ll&&(De("WebGLShadowMap: PCFSoftShadowMap has been removed. Using PCFShadowMap instead."),this.type=fs);const I=i.getRenderTarget(),C=i.getActiveCubeFace(),U=i.getActiveMipmapLevel(),F=i.state;F.setBlending(mi),F.buffers.depth.getReversed()===!0?F.buffers.color.setClear(0,0,0,0):F.buffers.color.setClear(1,1,1,1),F.buffers.depth.setTest(!0),F.setScissorTest(!1);const X=u!==this.type;X&&h.traverse(function(L){L.material&&(Array.isArray(L.material)?L.material.forEach(W=>W.needsUpdate=!0):L.material.needsUpdate=!0)});for(let L=0,W=A.length;L<W;L++){const K=A[L],H=K.shadow;if(H===void 0){De("WebGLShadowMap:",K,"has no shadow.");continue}if(H.autoUpdate===!1&&H.needsUpdate===!1)continue;s.copy(H.mapSize);const ne=H.getFrameExtents();s.multiply(ne),r.copy(H.mapSize),(s.x>m||s.y>m)&&(s.x>m&&(r.x=Math.floor(m/ne.x),s.x=r.x*ne.x,H.mapSize.x=r.x),s.y>m&&(r.y=Math.floor(m/ne.y),s.y=r.y*ne.y,H.mapSize.y=r.y));const q=i.state.buffers.depth.getReversed();if(H.camera._reversedDepth=q,H.map===null||X===!0){if(H.map!==null&&(H.map.depthTexture!==null&&(H.map.depthTexture.dispose(),H.map.depthTexture=null),H.map.dispose()),this.type===Ci){if(K.isPointLight){De("WebGLShadowMap: VSM shadow maps are not supported for PointLights. Use PCF or BasicShadowMap instead.");continue}H.map=new mn(s.x,s.y,{format:Qn,type:xn,minFilter:Wt,magFilter:Wt,generateMipmaps:!1}),H.map.texture.name=K.name+".shadowMap",H.map.depthTexture=new vi(s.x,s.y,hn),H.map.depthTexture.name=K.name+".shadowMapDepth",H.map.depthTexture.format=zn,H.map.depthTexture.compareFunction=null,H.map.depthTexture.minFilter=Ct,H.map.depthTexture.magFilter=Ct}else K.isPointLight?(H.map=new ha(s.x),H.map.depthTexture=new nl(s.x,gn)):(H.map=new mn(s.x,s.y),H.map.depthTexture=new vi(s.x,s.y,gn)),H.map.depthTexture.name=K.name+".shadowMap",H.map.depthTexture.format=zn,this.type===fs?(H.map.depthTexture.compareFunction=q?Yo:qo,H.map.depthTexture.minFilter=Wt,H.map.depthTexture.magFilter=Wt):(H.map.depthTexture.compareFunction=null,H.map.depthTexture.minFilter=Ct,H.map.depthTexture.magFilter=Ct);H.camera.updateProjectionMatrix()}H.map.isWebGLCubeRenderTarget!==!0&&(H.map.width!==s.x||H.map.height!==s.y)&&H.map.setSize(s.x,s.y);const ee=H.map.isWebGLCubeRenderTarget?6:H.getViewportCount();K.isPointLight!==!0&&H.updateMatrices(K,M);const te=(H.camera.layers.mask&4294967294)!==0?H.camera.layers:M.layers;for(let Me=0;Me<ee;Me++){const Le=H.getCamera(Me);if(K.isPointLight){const $=H.camera,pe=H.matrix,ve=K.distance||$.far;ve!==$.far&&($.far=ve,$.updateProjectionMatrix()),Ri.setFromMatrixPosition(K.matrixWorld),$.position.copy(Ri),lr.copy($.position),lr.add(Ip[Me]),$.up.copy(Np[Me]),$.lookAt(lr),$.updateMatrixWorld(),pe.makeTranslation(-Ri.x,-Ri.y,-Ri.z),wo.multiplyMatrices($.projectionMatrix,$.matrixWorldInverse),H._frustum.setFromProjectionMatrix(wo,$.coordinateSystem,$.reversedDepth)}if(H.map.isWebGLCubeRenderTarget)i.setRenderTarget(H.map,Me),i.clear();else{Me===0&&(i.setRenderTarget(H.map),i.clear());const $=H.getViewport(Me);a.set(r.x*$.x,r.y*$.y,r.x*$.z,r.y*$.w),F.viewport(a)}n=H.getFrustum(Me),E(h,M,Le,K,te,this.type)}H.isPointLightShadow!==!0&&this.type===Ci&&b(H,M),H.needsUpdate=!1}u=this.type,p.needsUpdate=!1,i.setRenderTarget(I,C,U)};function b(A,h){const M=e.update(T);f.defines.VSM_SAMPLES!==A.blurSamples&&(f.defines.VSM_SAMPLES=A.blurSamples,g.defines.VSM_SAMPLES=A.blurSamples,f.needsUpdate=!0,g.needsUpdate=!0),A.mapPass===null?A.mapPass=new mn(s.x,s.y,{format:Qn,type:xn}):(A.mapPass.width!==A.map.width||A.mapPass.height!==A.map.height)&&A.mapPass.setSize(A.map.width,A.map.height),f.uniforms.shadow_pass.value=A.map.depthTexture,f.uniforms.resolution.value.set(A.map.width,A.map.height),f.uniforms.radius.value=A.radius,i.setRenderTarget(A.mapPass),i.clear(),i.renderBufferDirect(h,null,M,f,T,null),g.uniforms.shadow_pass.value=A.mapPass.texture,g.uniforms.resolution.value.set(A.map.width,A.map.height),g.uniforms.radius.value=A.radius,i.setRenderTarget(A.map),i.clear(),i.renderBufferDirect(h,null,M,g,T,null)}function y(A,h,M,I){let C=null;const U=M.isPointLight===!0?A.customDistanceMaterial:A.customDepthMaterial;if(U!==void 0)C=U;else if(C=M.isPointLight===!0?d:o,i.localClippingEnabled&&h.clipShadows===!0&&Array.isArray(h.clippingPlanes)&&h.clippingPlanes.length!==0||h.displacementMap&&h.displacementScale!==0||h.alphaMap&&h.alphaTest>0||h.map&&h.alphaTest>0||h.alphaToCoverage===!0){const F=C.uuid,X=h.uuid;let L=c[F];L===void 0&&(L={},c[F]=L);let W=L[X];W===void 0&&(W=C.clone(),L[X]=W,h.addEventListener("dispose",R)),C=W}if(C.visible=h.visible,C.wireframe=h.wireframe,I===Ci?C.side=h.shadowSide!==null?h.shadowSide:h.side:C.side=h.shadowSide!==null?h.shadowSide:x[h.side],C.alphaMap=h.alphaMap,C.alphaTest=h.alphaToCoverage===!0?.5:h.alphaTest,C.map=h.map,C.clipShadows=h.clipShadows,C.clippingPlanes=h.clippingPlanes,C.clipIntersection=h.clipIntersection,C.displacementMap=h.displacementMap,C.displacementScale=h.displacementScale,C.displacementBias=h.displacementBias,C.wireframeLinewidth=h.wireframeLinewidth,C.linewidth=h.linewidth,M.isPointLight===!0&&C.isMeshDistanceMaterial===!0){const F=i.properties.get(C);F.light=M}return C}function E(A,h,M,I,C,U){if(A.visible===!1)return;if(A.layers.test(C)&&(A.isMesh||A.isLine||A.isPoints)&&(A.castShadow||A.receiveShadow&&U===Ci)&&(!A.frustumCulled||A.intersectsFrustum(n))){A.modelViewMatrix.multiplyMatrices(M.matrixWorldInverse,A.matrixWorld);const L=e.update(A),W=A.material;if(Array.isArray(W)){const K=L.groups;for(let H=0,ne=K.length;H<ne;H++){const q=K[H],ee=W[q.materialIndex];if(ee&&ee.visible){const te=y(A,ee,I,U);A.onBeforeShadow(i,A,h,M,L,te,q),i.renderBufferDirect(M,null,L,te,A,q),A.onAfterShadow(i,A,h,M,L,te,q)}}}else if(W.visible){const K=y(A,W,I,U);A.onBeforeShadow(i,A,h,M,L,K,null),i.renderBufferDirect(M,null,L,K,A,null),A.onAfterShadow(i,A,h,M,L,K,null)}}const X=A.children;for(let L=0,W=X.length;L<W;L++)E(X[L],h,M,I,C,U)}function R(A){A.target.removeEventListener("dispose",R);for(const M in c){const I=c[M],C=A.target.uuid;C in I&&(I[C].dispose(),delete I[C])}}}function Fp(i,e){function t(){let P=!1;const Q=new pt;let Y=null;const de=new pt(0,0,0,0);return{setMask:function(re){Y!==re&&!P&&(i.colorMask(re,re,re,re),Y=re)},setLocked:function(re){P=re},setClear:function(re,oe,Te,He,se){se===!0&&(re*=He,oe*=He,Te*=He),Q.set(re,oe,Te,He),de.equals(Q)===!1&&(i.clearColor(re,oe,Te,He),de.copy(Q))},reset:function(){P=!1,Y=null,de.set(-1,0,0,0)}}}function n(){let P=!1,Q=!1,Y=null,de=null,re=null;return{setReversed:function(oe){if(Q!==oe){const Te=e.get("EXT_clip_control");oe?Te.clipControlEXT(Te.LOWER_LEFT_EXT,Te.ZERO_TO_ONE_EXT):Te.clipControlEXT(Te.LOWER_LEFT_EXT,Te.NEGATIVE_ONE_TO_ONE_EXT),Q=oe;const He=re;re=null,this.setClear(He)}},getReversed:function(){return Q},setTest:function(oe){oe?me(i.DEPTH_TEST):Ie(i.DEPTH_TEST)},setMask:function(oe){Y!==oe&&!P&&(i.depthMask(oe),Y=oe)},setFunc:function(oe){if(Q&&(oe=uc[oe]),de!==oe){switch(oe){case cr:i.depthFunc(i.NEVER);break;case dr:i.depthFunc(i.ALWAYS);break;case ur:i.depthFunc(i.LESS);break;case Di:i.depthFunc(i.LEQUAL);break;case fr:i.depthFunc(i.EQUAL);break;case hr:i.depthFunc(i.GEQUAL);break;case pr:i.depthFunc(i.GREATER);break;case mr:i.depthFunc(i.NOTEQUAL);break;default:i.depthFunc(i.LEQUAL)}de=oe}},setLocked:function(oe){P=oe},setClear:function(oe){re!==oe&&(re=oe,Q&&(oe=1-oe),i.clearDepth(oe))},reset:function(){P=!1,Y=null,de=null,re=null,Q=!1}}}function s(){let P=!1,Q=null,Y=null,de=null,re=null,oe=null,Te=null,He=null,se=null;return{setTest:function(we){P||(we?me(i.STENCIL_TEST):Ie(i.STENCIL_TEST))},setMask:function(we){Q!==we&&!P&&(i.stencilMask(we),Q=we)},setFunc:function(we,ze,ge){(Y!==we||de!==ze||re!==ge)&&(i.stencilFunc(we,ze,ge),Y=we,de=ze,re=ge)},setOp:function(we,ze,ge){(oe!==we||Te!==ze||He!==ge)&&(i.stencilOp(we,ze,ge),oe=we,Te=ze,He=ge)},setLocked:function(we){P=we},setClear:function(we){se!==we&&(i.clearStencil(we),se=we)},reset:function(){P=!1,Q=null,Y=null,de=null,re=null,oe=null,Te=null,He=null,se=null}}}const r=new t,a=new n,o=new s;let d={};const c=new WeakMap;let m={},x={},f={},g=new WeakMap,l=[],T=null,p=!1,u=null,b=null,y=null,E=null,R=null,A=null,h=null,M=new je(0,0,0),I=0,C=!1,U=null,F=null,X=null,L=null,W=null;const K=i.getParameter(i.MAX_COMBINED_TEXTURE_IMAGE_UNITS);let H=!1,ne=0;const q=i.getParameter(i.VERSION);q.indexOf("WebGL")!==-1?(ne=parseFloat(/^WebGL (\d)/.exec(q)[1]),H=ne>=1):q.indexOf("OpenGL ES")!==-1&&(ne=parseFloat(/^OpenGL ES (\d)/.exec(q)[1]),H=ne>=2);let ee=null,te={};const Me=i.getParameter(i.SCISSOR_BOX),Le=i.getParameter(i.VIEWPORT),$=new pt().fromArray(Me),pe=new pt().fromArray(Le);function ve(P,Q,Y,de){const re=new Uint8Array(4),oe=i.createTexture();i.bindTexture(P,oe),i.texParameteri(P,i.TEXTURE_MIN_FILTER,i.NEAREST),i.texParameteri(P,i.TEXTURE_MAG_FILTER,i.NEAREST);for(let Te=0;Te<Y;Te++)P===i.TEXTURE_3D||P===i.TEXTURE_2D_ARRAY?i.texImage3D(Q,0,i.RGBA,1,1,de,0,i.RGBA,i.UNSIGNED_BYTE,re):i.texImage2D(Q+Te,0,i.RGBA,1,1,0,i.RGBA,i.UNSIGNED_BYTE,re);return oe}const Ae={};Ae[i.TEXTURE_2D]=ve(i.TEXTURE_2D,i.TEXTURE_2D,1),Ae[i.TEXTURE_CUBE_MAP]=ve(i.TEXTURE_CUBE_MAP,i.TEXTURE_CUBE_MAP_POSITIVE_X,6),Ae[i.TEXTURE_2D_ARRAY]=ve(i.TEXTURE_2D_ARRAY,i.TEXTURE_2D_ARRAY,1,1),Ae[i.TEXTURE_3D]=ve(i.TEXTURE_3D,i.TEXTURE_3D,1,1),r.setClear(0,0,0,1),a.setClear(1),o.setClear(0),me(i.DEPTH_TEST),a.setFunc(Di),Pt(!1),ct(Pa),me(i.CULL_FACE),bt(mi);function me(P){m[P]!==!0&&(i.enable(P),m[P]=!0)}function Ie(P){m[P]!==!1&&(i.disable(P),m[P]=!1)}function gt(P,Q){return f[P]!==Q?(i.bindFramebuffer(P,Q),f[P]=Q,P===i.DRAW_FRAMEBUFFER&&(f[i.FRAMEBUFFER]=Q),P===i.FRAMEBUFFER&&(f[i.DRAW_FRAMEBUFFER]=Q),!0):!1}function Re(P,Q){let Y=l,de=!1;if(P){Y=g.get(Q),Y===void 0&&(Y=[],g.set(Q,Y));const re=P.textures;if(Y.length!==re.length||Y[0]!==i.COLOR_ATTACHMENT0){for(let oe=0,Te=re.length;oe<Te;oe++)Y[oe]=i.COLOR_ATTACHMENT0+oe;Y.length=re.length,de=!0}}else Y[0]!==i.BACK&&(Y[0]=i.BACK,de=!0);de&&i.drawBuffers(Y)}function et(P){return T!==P?(i.useProgram(P),T=P,!0):!1}const it={[pi]:i.FUNC_ADD,[Il]:i.FUNC_SUBTRACT,[Nl]:i.FUNC_REVERSE_SUBTRACT};it[Ul]=i.MIN,it[Fl]=i.MAX;const Ge={[Ol]:i.ZERO,[Bl]:i.ONE,[kl]:i.SRC_COLOR,[Co]:i.SRC_ALPHA,[Xl]:i.SRC_ALPHA_SATURATE,[Vl]:i.DST_COLOR,[Gl]:i.DST_ALPHA,[zl]:i.ONE_MINUS_SRC_COLOR,[Po]:i.ONE_MINUS_SRC_ALPHA,[Wl]:i.ONE_MINUS_DST_COLOR,[Hl]:i.ONE_MINUS_DST_ALPHA,[ql]:i.CONSTANT_COLOR,[Yl]:i.ONE_MINUS_CONSTANT_COLOR,[Kl]:i.CONSTANT_ALPHA,[Zl]:i.ONE_MINUS_CONSTANT_ALPHA};function bt(P,Q,Y,de,re,oe,Te,He,se,we){if(P===mi){p===!0&&(Ie(i.BLEND),p=!1);return}if(p===!1&&(me(i.BLEND),p=!0),P!==Dl){if(P!==u||we!==C){if((b!==pi||R!==pi)&&(i.blendEquation(i.FUNC_ADD),b=pi,R=pi),we)switch(P){case Li:i.blendFuncSeparate(i.ONE,i.ONE_MINUS_SRC_ALPHA,i.ONE,i.ONE_MINUS_SRC_ALPHA);break;case La:i.blendFunc(i.ONE,i.ONE);break;case Da:i.blendFuncSeparate(i.ZERO,i.ONE_MINUS_SRC_COLOR,i.ZERO,i.ONE);break;case Ia:i.blendFuncSeparate(i.DST_COLOR,i.ONE_MINUS_SRC_ALPHA,i.ZERO,i.ONE);break;default:nt("WebGLState: Invalid blending: ",P);break}else switch(P){case Li:i.blendFuncSeparate(i.SRC_ALPHA,i.ONE_MINUS_SRC_ALPHA,i.ONE,i.ONE_MINUS_SRC_ALPHA);break;case La:i.blendFuncSeparate(i.SRC_ALPHA,i.ONE,i.ONE,i.ONE);break;case Da:nt("WebGLState: SubtractiveBlending requires material.premultipliedAlpha = true");break;case Ia:nt("WebGLState: MultiplyBlending requires material.premultipliedAlpha = true");break;default:nt("WebGLState: Invalid blending: ",P);break}y=null,E=null,A=null,h=null,M.set(0,0,0),I=0,u=P,C=we}return}re=re||Q,oe=oe||Y,Te=Te||de,(Q!==b||re!==R)&&(i.blendEquationSeparate(it[Q],it[re]),b=Q,R=re),(Y!==y||de!==E||oe!==A||Te!==h)&&(i.blendFuncSeparate(Ge[Y],Ge[de],Ge[oe],Ge[Te]),y=Y,E=de,A=oe,h=Te),(He.equals(M)===!1||se!==I)&&(i.blendColor(He.r,He.g,He.b,se),M.copy(He),I=se),u=P,C=!1}function xt(P,Q){P.side===En?Ie(i.CULL_FACE):me(i.CULL_FACE);let Y=P.side===Xt;Q&&(Y=!Y),Pt(Y),P.blending===Li&&P.transparent===!1?bt(mi):bt(P.blending,P.blendEquation,P.blendSrc,P.blendDst,P.blendEquationAlpha,P.blendSrcAlpha,P.blendDstAlpha,P.blendColor,P.blendAlpha,P.premultipliedAlpha),a.setFunc(P.depthFunc),a.setTest(P.depthTest),a.setMask(P.depthWrite),r.setMask(P.colorWrite);const de=P.stencilWrite;o.setTest(de),de&&(o.setMask(P.stencilWriteMask),o.setFunc(P.stencilFunc,P.stencilRef,P.stencilFuncMask),o.setOp(P.stencilFail,P.stencilZFail,P.stencilZPass)),vt(P.polygonOffset,P.polygonOffsetFactor,P.polygonOffsetUnits),P.alphaToCoverage===!0?me(i.SAMPLE_ALPHA_TO_COVERAGE):Ie(i.SAMPLE_ALPHA_TO_COVERAGE)}function Pt(P){U!==P&&(P?i.frontFace(i.CW):i.frontFace(i.CCW),U=P)}function ct(P){P!==Cl?(me(i.CULL_FACE),P!==F&&(P===Pa?i.cullFace(i.BACK):P===Pl?i.cullFace(i.FRONT):i.cullFace(i.FRONT_AND_BACK))):Ie(i.CULL_FACE),F=P}function qt(P){P!==X&&(H&&i.lineWidth(P),X=P)}function vt(P,Q,Y){P?(me(i.POLYGON_OFFSET_FILL),(L!==Q||W!==Y)&&(L=Q,W=Y,a.getReversed()&&(Q=-Q,Y=-Y),i.polygonOffset(Q,Y))):Ie(i.POLYGON_OFFSET_FILL)}function Ft(P){P?me(i.SCISSOR_TEST):Ie(i.SCISSOR_TEST)}function Ot(P){P===void 0&&(P=i.TEXTURE0+K-1),ee!==P&&(i.activeTexture(P),ee=P)}function Gt(P,Q,Y){Y===void 0&&(ee===null?Y=i.TEXTURE0+K-1:Y=ee);let de=te[Y];de===void 0&&(de={type:void 0,texture:void 0},te[Y]=de),(de.type!==P||de.texture!==Q)&&(ee!==Y&&(i.activeTexture(Y),ee=Y),i.bindTexture(P,Q||Ae[P]),de.type=P,de.texture=Q)}function N(){const P=te[ee];P!==void 0&&P.type!==void 0&&(i.bindTexture(P.type,null),P.type=void 0,P.texture=void 0)}function Ht(){try{i.compressedTexImage2D(...arguments)}catch(P){nt("WebGLState:",P)}}function ot(){try{i.compressedTexImage3D(...arguments)}catch(P){nt("WebGLState:",P)}}function dt(){try{i.texSubImage2D(...arguments)}catch(P){nt("WebGLState:",P)}}function le(){try{i.texSubImage3D(...arguments)}catch(P){nt("WebGLState:",P)}}function st(){try{i.compressedTexSubImage2D(...arguments)}catch(P){nt("WebGLState:",P)}}function Ce(){try{i.compressedTexSubImage3D(...arguments)}catch(P){nt("WebGLState:",P)}}function Pe(){try{i.texStorage2D(...arguments)}catch(P){nt("WebGLState:",P)}}function w(){try{i.texStorage3D(...arguments)}catch(P){nt("WebGLState:",P)}}function v(){try{i.texImage2D(...arguments)}catch(P){nt("WebGLState:",P)}}function B(){try{i.texImage3D(...arguments)}catch(P){nt("WebGLState:",P)}}function ie(P){return x[P]!==void 0?x[P]:i.getParameter(P)}function j(P,Q){x[P]!==Q&&(i.pixelStorei(P,Q),x[P]=Q)}function ae(P){$.equals(P)===!1&&(i.scissor(P.x,P.y,P.z,P.w),$.copy(P))}function fe(P){pe.equals(P)===!1&&(i.viewport(P.x,P.y,P.z,P.w),pe.copy(P))}function Z(P,Q){let Y=c.get(Q);Y===void 0&&(Y=new WeakMap,c.set(Q,Y));let de=Y.get(P);de===void 0&&(de=i.getUniformBlockIndex(Q,P.name),i.uniformBlockBinding(Q,de,de),Y.set(P,de))}function J(P,Q,Y){const re=c.get(Q).get(P);d[re]!==Y&&(i.bindBufferBase(i.UNIFORM_BUFFER,re,Y),d[re]=Y)}function ce(){i.disable(i.BLEND),i.disable(i.CULL_FACE),i.disable(i.DEPTH_TEST),i.disable(i.POLYGON_OFFSET_FILL),i.disable(i.SCISSOR_TEST),i.disable(i.STENCIL_TEST),i.disable(i.SAMPLE_ALPHA_TO_COVERAGE),i.blendEquation(i.FUNC_ADD),i.blendFunc(i.ONE,i.ZERO),i.blendFuncSeparate(i.ONE,i.ZERO,i.ONE,i.ZERO),i.blendColor(0,0,0,0),i.colorMask(!0,!0,!0,!0),i.clearColor(0,0,0,0),i.depthMask(!0),i.depthFunc(i.LESS),a.setReversed(!1),i.clearDepth(1),i.stencilMask(4294967295),i.stencilFunc(i.ALWAYS,0,4294967295),i.stencilOp(i.KEEP,i.KEEP,i.KEEP),i.clearStencil(0),i.cullFace(i.BACK),i.frontFace(i.CCW),i.polygonOffset(0,0),i.activeTexture(i.TEXTURE0),i.bindFramebuffer(i.FRAMEBUFFER,null),i.bindFramebuffer(i.DRAW_FRAMEBUFFER,null),i.bindFramebuffer(i.READ_FRAMEBUFFER,null),i.useProgram(null),i.lineWidth(1),i.scissor(0,0,i.canvas.width,i.canvas.height),i.viewport(0,0,i.canvas.width,i.canvas.height),i.pixelStorei(i.PACK_ALIGNMENT,4),i.pixelStorei(i.UNPACK_ALIGNMENT,4),i.pixelStorei(i.UNPACK_FLIP_Y_WEBGL,!1),i.pixelStorei(i.UNPACK_PREMULTIPLY_ALPHA_WEBGL,!1),i.pixelStorei(i.UNPACK_COLORSPACE_CONVERSION_WEBGL,i.BROWSER_DEFAULT_WEBGL),i.pixelStorei(i.PACK_ROW_LENGTH,0),i.pixelStorei(i.PACK_SKIP_PIXELS,0),i.pixelStorei(i.PACK_SKIP_ROWS,0),i.pixelStorei(i.UNPACK_ROW_LENGTH,0),i.pixelStorei(i.UNPACK_IMAGE_HEIGHT,0),i.pixelStorei(i.UNPACK_SKIP_PIXELS,0),i.pixelStorei(i.UNPACK_SKIP_ROWS,0),i.pixelStorei(i.UNPACK_SKIP_IMAGES,0),m={},x={},ee=null,te={},f={},d={},g=new WeakMap,l=[],T=null,p=!1,u=null,b=null,y=null,E=null,R=null,A=null,h=null,M=new je(0,0,0),I=0,C=!1,U=null,F=null,X=null,L=null,W=null,$.set(0,0,i.canvas.width,i.canvas.height),pe.set(0,0,i.canvas.width,i.canvas.height),r.reset(),a.reset(),o.reset()}return{buffers:{color:r,depth:a,stencil:o},enable:me,disable:Ie,bindFramebuffer:gt,drawBuffers:Re,useProgram:et,setBlending:bt,setMaterial:xt,setFlipSided:Pt,setCullFace:ct,setLineWidth:qt,setPolygonOffset:vt,setScissorTest:Ft,activeTexture:Ot,bindTexture:Gt,unbindTexture:N,compressedTexImage2D:Ht,compressedTexImage3D:ot,texImage2D:v,texImage3D:B,pixelStorei:j,getParameter:ie,updateUBOMapping:Z,uniformBlockBinding:J,texStorage2D:Pe,texStorage3D:w,texSubImage2D:dt,texSubImage3D:le,compressedTexSubImage2D:st,compressedTexSubImage3D:Ce,scissor:ae,viewport:fe,reset:ce}}function Op(i,e,t,n,s,r,a){const o=e.has("WEBGL_multisampled_render_to_texture")?e.get("WEBGL_multisampled_render_to_texture"):null,d=typeof navigator>"u"?!1:/OculusBrowser/g.test(navigator.userAgent),c=new Xe,m=new WeakMap,x=new Set;let f;const g=new WeakMap,l=new Set,T=new FinalizationRegistry(w=>l.delete(w));let p=!1;try{p=typeof OffscreenCanvas<"u"&&new OffscreenCanvas(1,1).getContext("2d")!==null}catch{}function u(w,v){return p?new OffscreenCanvas(w,v):ys("canvas")}function b(w,v,B){let ie=1;const j=Pe(w);if((j.width>B||j.height>B)&&(ie=B/Math.max(j.width,j.height)),ie<1)if(typeof HTMLImageElement<"u"&&w instanceof HTMLImageElement||typeof HTMLCanvasElement<"u"&&w instanceof HTMLCanvasElement||typeof ImageBitmap<"u"&&w instanceof ImageBitmap||typeof VideoFrame<"u"&&w instanceof VideoFrame){const ae=Math.floor(ie*j.width),fe=Math.floor(ie*j.height);f===void 0&&(f=u(ae,fe));const Z=v?u(ae,fe):f;return Z.width=ae,Z.height=fe,Z.getContext("2d").drawImage(w,0,0,ae,fe),De("WebGLRenderer: Texture has been resized from ("+j.width+"x"+j.height+") to ("+ae+"x"+fe+")."),Z}else return"data"in w&&De("WebGLRenderer: Image in DataTexture is too big ("+j.width+"x"+j.height+")."),w;return w}function y(w){return w.generateMipmaps}function E(w){i.generateMipmap(w)}function R(w){return w.isWebGLCubeRenderTarget?i.TEXTURE_CUBE_MAP:w.isWebGL3DRenderTarget?i.TEXTURE_3D:w.isWebGLArrayRenderTarget||w.isCompressedArrayTexture?i.TEXTURE_2D_ARRAY:i.TEXTURE_2D}function A(w,v,B,ie,j,ae=!1){if(w!==null){if(i[w]!==void 0)return i[w];De("WebGLRenderer: Attempt to use non-existing WebGL internal format '"+w+"'")}let fe;ie&&(fe=e.get("EXT_texture_norm16"),fe||De("WebGLRenderer: Unable to use normalized textures without EXT_texture_norm16 extension"));let Z=v;if(v===i.RED&&(B===i.FLOAT&&(Z=i.R32F),B===i.HALF_FLOAT&&(Z=i.R16F),B===i.UNSIGNED_BYTE&&(Z=i.R8),B===i.UNSIGNED_SHORT&&fe&&(Z=fe.R16_EXT),B===i.SHORT&&fe&&(Z=fe.R16_SNORM_EXT)),v===i.RED_INTEGER&&(B===i.UNSIGNED_BYTE&&(Z=i.R8UI),B===i.UNSIGNED_SHORT&&(Z=i.R16UI),B===i.UNSIGNED_INT&&(Z=i.R32UI),B===i.BYTE&&(Z=i.R8I),B===i.SHORT&&(Z=i.R16I),B===i.INT&&(Z=i.R32I)),v===i.RG&&(B===i.FLOAT&&(Z=i.RG32F),B===i.HALF_FLOAT&&(Z=i.RG16F),B===i.UNSIGNED_BYTE&&(Z=i.RG8),B===i.UNSIGNED_SHORT&&fe&&(Z=fe.RG16_EXT),B===i.SHORT&&fe&&(Z=fe.RG16_SNORM_EXT)),v===i.RG_INTEGER&&(B===i.UNSIGNED_BYTE&&(Z=i.RG8UI),B===i.UNSIGNED_SHORT&&(Z=i.RG16UI),B===i.UNSIGNED_INT&&(Z=i.RG32UI),B===i.BYTE&&(Z=i.RG8I),B===i.SHORT&&(Z=i.RG16I),B===i.INT&&(Z=i.RG32I)),v===i.RGB_INTEGER&&(B===i.UNSIGNED_BYTE&&(Z=i.RGB8UI),B===i.UNSIGNED_SHORT&&(Z=i.RGB16UI),B===i.UNSIGNED_INT&&(Z=i.RGB32UI),B===i.BYTE&&(Z=i.RGB8I),B===i.SHORT&&(Z=i.RGB16I),B===i.INT&&(Z=i.RGB32I)),v===i.RGBA_INTEGER&&(B===i.UNSIGNED_BYTE&&(Z=i.RGBA8UI),B===i.UNSIGNED_SHORT&&(Z=i.RGBA16UI),B===i.UNSIGNED_INT&&(Z=i.RGBA32UI),B===i.BYTE&&(Z=i.RGBA8I),B===i.SHORT&&(Z=i.RGBA16I),B===i.INT&&(Z=i.RGBA32I)),v===i.RGB&&(B===i.UNSIGNED_SHORT&&fe&&(Z=fe.RGB16_EXT),B===i.SHORT&&fe&&(Z=fe.RGB16_SNORM_EXT),B===i.UNSIGNED_INT_5_9_9_9_REV&&(Z=i.RGB9_E5),B===i.UNSIGNED_INT_10F_11F_11F_REV&&(Z=i.R11F_G11F_B10F)),v===i.RGBA){const J=ae?bs:Ze.getTransfer(j);B===i.FLOAT&&(Z=i.RGBA32F),B===i.HALF_FLOAT&&(Z=i.RGBA16F),B===i.UNSIGNED_BYTE&&(Z=J===at?i.SRGB8_ALPHA8:i.RGBA8),B===i.UNSIGNED_SHORT&&fe&&(Z=fe.RGBA16_EXT),B===i.SHORT&&fe&&(Z=fe.RGBA16_SNORM_EXT),B===i.UNSIGNED_SHORT_4_4_4_4&&(Z=i.RGBA4),B===i.UNSIGNED_SHORT_5_5_5_1&&(Z=i.RGB5_A1)}return(Z===i.R16F||Z===i.R32F||Z===i.RG16F||Z===i.RG32F||Z===i.RGBA16F||Z===i.RGBA32F)&&e.get("EXT_color_buffer_float"),Z}function h(w,v){let B;return w?v===null||v===gn||v===_i?B=i.DEPTH24_STENCIL8:v===hn?B=i.DEPTH32F_STENCIL8:v===Ni&&(B=i.DEPTH24_STENCIL8,De("DepthTexture: 16 bit depth attachment is not supported with stencil. Using 24-bit attachment.")):v===null||v===gn||v===_i?B=i.DEPTH_COMPONENT24:v===hn?B=i.DEPTH_COMPONENT32F:v===Ni&&(B=i.DEPTH_COMPONENT16),B}function M(w,v){return y(w)===!0||w.isFramebufferTexture&&w.minFilter!==Ct&&w.minFilter!==Wt?Math.log2(Math.max(v.width,v.height))+1:w.mipmaps.length>0?w.mipmaps.length:w.isCompressedTexture&&Array.isArray(w.image)?v.mipmaps.length:1}function I(w){const v=new WeakRef(w);n.get(w).__ref=v,l.add(v),T.register(w,v,v)}function C(w){const v=n.get(w).__ref;v!==void 0&&(l.delete(v),T.unregister(v))}function U(w){X(w.target)}function F(w){L(w.target)}function X(w){w.removeEventListener("dispose",U),C(w),K(w),w.isVideoTexture&&m.delete(w),w.isHTMLTexture&&x.delete(w)}function L(w){w.removeEventListener("dispose",F),C(w),ne(w)}function W(){for(const w of l){const v=w.deref();v!==void 0&&(v.isRenderTarget===!0?L(v):X(v))}l.clear(),x.clear()}function K(w){const v=n.get(w);if(v.__webglInit===void 0)return;const B=w.source,ie=g.get(B);if(ie){const j=ie[v.__cacheKey];j.usedTimes--,j.usedTimes===0&&H(w),Object.keys(ie).length===0&&g.delete(B)}n.remove(w)}function H(w){const v=n.get(w);i.deleteTexture(v.__webglTexture);const B=w.source,ie=g.get(B);delete ie[v.__cacheKey],a.memory.textures--}function ne(w){const v=n.get(w);v.__depthDisposeCallback&&v.__depthDisposeCallback();const B=w.depthTexture;if(B&&B.renderTarget===w&&X(B),w.isWebGLCubeRenderTarget)for(let j=0;j<6;j++){if(Array.isArray(v.__webglFramebuffer[j]))for(let ae=0;ae<v.__webglFramebuffer[j].length;ae++)i.deleteFramebuffer(v.__webglFramebuffer[j][ae]);else i.deleteFramebuffer(v.__webglFramebuffer[j]);v.__webglDepthbuffer&&i.deleteRenderbuffer(v.__webglDepthbuffer[j])}else{if(Array.isArray(v.__webglFramebuffer))for(let j=0;j<v.__webglFramebuffer.length;j++)i.deleteFramebuffer(v.__webglFramebuffer[j]);else i.deleteFramebuffer(v.__webglFramebuffer);if(v.__webglDepthbuffer&&i.deleteRenderbuffer(v.__webglDepthbuffer),v.__webglMultisampledFramebuffer&&i.deleteFramebuffer(v.__webglMultisampledFramebuffer),v.__webglColorRenderbuffer)for(let j=0;j<v.__webglColorRenderbuffer.length;j++)v.__webglColorRenderbuffer[j]&&i.deleteRenderbuffer(v.__webglColorRenderbuffer[j]);v.__webglDepthRenderbuffer&&i.deleteRenderbuffer(v.__webglDepthRenderbuffer)}const ie=w.textures;for(let j=0,ae=ie.length;j<ae;j++){const fe=n.get(ie[j]);fe.__webglTexture&&(i.deleteTexture(fe.__webglTexture),a.memory.textures--),n.remove(ie[j])}n.remove(w)}let q=0;function ee(){q=0}function te(){return q}function Me(w){q=w}function Le(){const w=q;return w>=s.maxTextures&&De("WebGLTextures: Trying to use "+(w+1)+" texture units while this GPU supports only "+s.maxTextures),q+=1,w}function $(w){const v=[];return v.push(w.wrapS),v.push(w.wrapT),v.push(w.wrapR||0),v.push(w.magFilter),v.push(w.minFilter),v.push(w.anisotropy),v.push(w.internalFormat),v.push(w.format),v.push(w.type),v.push(w.generateMipmaps),v.push(w.mipmapsAutoUpdate),v.push(w.premultiplyAlpha),v.push(w.flipY),v.push(w.unpackAlignment),v.push(w.colorSpace),v.join()}function pe(w,v){const B=n.get(w);if(w.isVideoTexture&&st(w),w.isRenderTargetTexture===!1&&w.isExternalTexture!==!0&&w.version>0&&B.__version!==w.version){const ie=w.image;if(ie===null)De("WebGLRenderer: Texture marked for update but no image data found.");else if(ie.complete===!1)De("WebGLRenderer: Texture marked for update but image is incomplete");else{xt(B,w,v);return}}else w.isExternalTexture&&(B.__webglTexture=w.sourceTexture?w.sourceTexture:null);t.bindTexture(i.TEXTURE_2D,B.__webglTexture,i.TEXTURE0+v)}function ve(w,v){const B=n.get(w);if(w.isRenderTargetTexture===!1&&w.version>0&&B.__version!==w.version){xt(B,w,v);return}else w.isExternalTexture&&(B.__webglTexture=w.sourceTexture?w.sourceTexture:null);t.bindTexture(i.TEXTURE_2D_ARRAY,B.__webglTexture,i.TEXTURE0+v)}function Ae(w,v){const B=n.get(w);if(w.isRenderTargetTexture===!1&&w.version>0&&B.__version!==w.version){xt(B,w,v);return}t.bindTexture(i.TEXTURE_3D,B.__webglTexture,i.TEXTURE0+v)}function me(w,v){const B=n.get(w);if(w.version>0&&B.__version!==w.version){Pt(B,w,v);return}t.bindTexture(i.TEXTURE_CUBE_MAP,B.__webglTexture,i.TEXTURE0+v)}const Ie={[gr]:i.REPEAT,[Tn]:i.CLAMP_TO_EDGE,[xr]:i.MIRRORED_REPEAT},gt={[Ct]:i.NEAREST,[Ql]:i.NEAREST_MIPMAP_NEAREST,[Vi]:i.NEAREST_MIPMAP_LINEAR,[Wt]:i.LINEAR,[Fs]:i.LINEAR_MIPMAP_NEAREST,[On]:i.LINEAR_MIPMAP_LINEAR},Re={[nc]:i.NEVER,[Ko]:i.ALWAYS,[ic]:i.LESS,[qo]:i.LEQUAL,[sc]:i.EQUAL,[Yo]:i.GEQUAL,[rc]:i.GREATER,[ac]:i.NOTEQUAL};function et(w,v){if(v.type===hn&&e.has("OES_texture_float_linear")===!1&&(v.magFilter===Wt||v.magFilter===Fs||v.magFilter===Vi||v.magFilter===On||v.minFilter===Wt||v.minFilter===Fs||v.minFilter===Vi||v.minFilter===On)&&De("WebGLRenderer: Unable to use linear filtering with floating point textures. OES_texture_float_linear not supported on this device."),i.texParameteri(w,i.TEXTURE_WRAP_S,Ie[v.wrapS]),i.texParameteri(w,i.TEXTURE_WRAP_T,Ie[v.wrapT]),(w===i.TEXTURE_3D||w===i.TEXTURE_2D_ARRAY)&&i.texParameteri(w,i.TEXTURE_WRAP_R,Ie[v.wrapR]),i.texParameteri(w,i.TEXTURE_MAG_FILTER,gt[v.magFilter]),i.texParameteri(w,i.TEXTURE_MIN_FILTER,gt[v.minFilter]),v.compareFunction&&(i.texParameteri(w,i.TEXTURE_COMPARE_MODE,i.COMPARE_REF_TO_TEXTURE),i.texParameteri(w,i.TEXTURE_COMPARE_FUNC,Re[v.compareFunction])),e.has("EXT_texture_filter_anisotropic")===!0){if(v.magFilter===Ct||v.minFilter!==Vi&&v.minFilter!==On||v.type===hn&&e.has("OES_texture_float_linear")===!1)return;if(v.anisotropy>1||n.get(v).__currentAnisotropy){const B=e.get("EXT_texture_filter_anisotropic");i.texParameterf(w,B.TEXTURE_MAX_ANISOTROPY_EXT,Math.min(v.anisotropy,s.getMaxAnisotropy())),n.get(v).__currentAnisotropy=v.anisotropy}}}function it(w,v){let B=!1;w.__webglInit===void 0&&(w.__webglInit=!0,v.addEventListener("dispose",U),I(v));const ie=v.source;let j=g.get(ie);j===void 0&&(j={},g.set(ie,j));const ae=$(v);if(ae!==w.__cacheKey){j[ae]===void 0&&(j[ae]={texture:i.createTexture(),usedTimes:0},a.memory.textures++,B=!0),j[ae].usedTimes++;const fe=j[w.__cacheKey];fe!==void 0&&(j[w.__cacheKey].usedTimes--,fe.usedTimes===0&&H(v)),w.__cacheKey=ae,w.__webglTexture=j[ae].texture}return B}function Ge(w,v,B){return Math.floor(Math.floor(w/B)/v)}function bt(w,v,B,ie){const ae=w.updateRanges;if(ae.length===0)t.texSubImage2D(i.TEXTURE_2D,0,0,0,v.width,v.height,B,ie,v.data);else{ae.sort((P,Q)=>P.start-Q.start);let fe=0;for(let P=1;P<ae.length;P++){const Q=ae[fe],Y=ae[P],de=Q.start+Q.count,re=Ge(Y.start,v.width,4),oe=Ge(Q.start,v.width,4);Y.start<=de+1&&re===oe&&Ge(Y.start+Y.count-1,v.width,4)===re?Q.count=Math.max(Q.count,Y.start+Y.count-Q.start):(++fe,ae[fe]=Y)}ae.length=fe+1;const Z=t.getParameter(i.UNPACK_ROW_LENGTH),J=t.getParameter(i.UNPACK_SKIP_PIXELS),ce=t.getParameter(i.UNPACK_SKIP_ROWS);t.pixelStorei(i.UNPACK_ROW_LENGTH,v.width);for(let P=0,Q=ae.length;P<Q;P++){const Y=ae[P],de=Math.floor(Y.start/4),re=Math.ceil(Y.count/4),oe=de%v.width,Te=Math.floor(de/v.width),He=re,se=1;t.pixelStorei(i.UNPACK_SKIP_PIXELS,oe),t.pixelStorei(i.UNPACK_SKIP_ROWS,Te),t.texSubImage2D(i.TEXTURE_2D,0,oe,Te,He,se,B,ie,v.data)}w.clearUpdateRanges(),t.pixelStorei(i.UNPACK_ROW_LENGTH,Z),t.pixelStorei(i.UNPACK_SKIP_PIXELS,J),t.pixelStorei(i.UNPACK_SKIP_ROWS,ce)}}function xt(w,v,B){let ie=i.TEXTURE_2D;(v.isDataArrayTexture||v.isCompressedArrayTexture)&&(ie=i.TEXTURE_2D_ARRAY),v.isData3DTexture&&(ie=i.TEXTURE_3D);const j=it(w,v),ae=v.source;t.bindTexture(ie,w.__webglTexture,i.TEXTURE0+B);const fe=n.get(ae);if(ae.version!==fe.__version||j===!0){if(t.activeTexture(i.TEXTURE0+B),(typeof ImageBitmap<"u"&&v.image instanceof ImageBitmap)===!1){const se=Ze.getPrimaries(Ze.workingColorSpace),we=v.colorSpace===Fn?null:Ze.getPrimaries(v.colorSpace),ze=v.colorSpace===Fn||se===we?i.NONE:i.BROWSER_DEFAULT_WEBGL;t.pixelStorei(i.UNPACK_FLIP_Y_WEBGL,v.flipY),t.pixelStorei(i.UNPACK_PREMULTIPLY_ALPHA_WEBGL,v.premultiplyAlpha),t.pixelStorei(i.UNPACK_COLORSPACE_CONVERSION_WEBGL,ze)}t.pixelStorei(i.UNPACK_ALIGNMENT,v.unpackAlignment);let J=b(v.image,!1,s.maxTextureSize);J=Ce(v,J);const ce=r.convert(v.format,v.colorSpace),P=r.convert(v.type);let Q=A(v.internalFormat,ce,P,v.normalized,v.colorSpace,v.isVideoTexture);et(ie,v);let Y;const de=v.mipmaps,re=v.isVideoTexture!==!0,oe=fe.__version===void 0||j===!0,Te=ae.dataReady,He=M(v,J);if(v.isDepthTexture)Q=h(v.format===Bn,v.type),oe&&(re?t.texStorage2D(i.TEXTURE_2D,1,Q,J.width,J.height):t.texImage2D(i.TEXTURE_2D,0,Q,J.width,J.height,0,ce,P,null));else if(v.isDataTexture)if(de.length>0){re&&oe&&t.texStorage2D(i.TEXTURE_2D,He,Q,de[0].width,de[0].height);for(let se=0,we=de.length;se<we;se++)Y=de[se],re?Te&&t.texSubImage2D(i.TEXTURE_2D,se,0,0,Y.width,Y.height,ce,P,Y.data):t.texImage2D(i.TEXTURE_2D,se,Q,Y.width,Y.height,0,ce,P,Y.data);v.generateMipmaps=!1}else re?(oe&&t.texStorage2D(i.TEXTURE_2D,He,Q,J.width,J.height),Te&&bt(v,J,ce,P)):t.texImage2D(i.TEXTURE_2D,0,Q,J.width,J.height,0,ce,P,J.data);else if(v.isCompressedTexture)if(v.isCompressedArrayTexture){re&&oe&&t.texStorage3D(i.TEXTURE_2D_ARRAY,He,Q,de[0].width,de[0].height,J.depth);for(let se=0,we=de.length;se<we;se++)if(Y=de[se],v.format!==pn)if(ce!==null)if(re){if(Te)if(v.layerUpdates.size>0){const ze=so(Y.width,Y.height,v.format,v.type);for(const ge of v.layerUpdates){const rt=Y.data.subarray(ge*ze/Y.data.BYTES_PER_ELEMENT,(ge+1)*ze/Y.data.BYTES_PER_ELEMENT);t.compressedTexSubImage3D(i.TEXTURE_2D_ARRAY,se,0,0,ge,Y.width,Y.height,1,ce,rt)}}else t.compressedTexSubImage3D(i.TEXTURE_2D_ARRAY,se,0,0,0,Y.width,Y.height,J.depth,ce,Y.data)}else t.compressedTexImage3D(i.TEXTURE_2D_ARRAY,se,Q,Y.width,Y.height,J.depth,0,Y.data,0,0);else De("WebGLRenderer: Attempt to load unsupported compressed texture format in .uploadTexture()");else re?Te&&t.texSubImage3D(i.TEXTURE_2D_ARRAY,se,0,0,0,Y.width,Y.height,J.depth,ce,P,Y.data):t.texImage3D(i.TEXTURE_2D_ARRAY,se,Q,Y.width,Y.height,J.depth,0,ce,P,Y.data);v.layerUpdates.size>0&&v.clearLayerUpdates()}else{re&&oe&&t.texStorage2D(i.TEXTURE_2D,He,Q,de[0].width,de[0].height);for(let se=0,we=de.length;se<we;se++)Y=de[se],v.format!==pn?ce!==null?re?Te&&t.compressedTexSubImage2D(i.TEXTURE_2D,se,0,0,Y.width,Y.height,ce,Y.data):t.compressedTexImage2D(i.TEXTURE_2D,se,Q,Y.width,Y.height,0,Y.data):De("WebGLRenderer: Attempt to load unsupported compressed texture format in .uploadTexture()"):re?Te&&t.texSubImage2D(i.TEXTURE_2D,se,0,0,Y.width,Y.height,ce,P,Y.data):t.texImage2D(i.TEXTURE_2D,se,Q,Y.width,Y.height,0,ce,P,Y.data)}else if(v.isDataArrayTexture)if(re){if(oe&&t.texStorage3D(i.TEXTURE_2D_ARRAY,He,Q,J.width,J.height,J.depth),Te)if(v.layerUpdates.size>0){const se=so(J.width,J.height,v.format,v.type);for(const we of v.layerUpdates){const ze=J.data.subarray(we*se/J.data.BYTES_PER_ELEMENT,(we+1)*se/J.data.BYTES_PER_ELEMENT);t.texSubImage3D(i.TEXTURE_2D_ARRAY,0,0,0,we,J.width,J.height,1,ce,P,ze)}v.clearLayerUpdates()}else t.texSubImage3D(i.TEXTURE_2D_ARRAY,0,0,0,0,J.width,J.height,J.depth,ce,P,J.data)}else t.texImage3D(i.TEXTURE_2D_ARRAY,0,Q,J.width,J.height,J.depth,0,ce,P,J.data);else if(v.isData3DTexture)re?(oe&&t.texStorage3D(i.TEXTURE_3D,He,Q,J.width,J.height,J.depth),Te&&t.texSubImage3D(i.TEXTURE_3D,0,0,0,0,J.width,J.height,J.depth,ce,P,J.data)):t.texImage3D(i.TEXTURE_3D,0,Q,J.width,J.height,J.depth,0,ce,P,J.data);else if(v.isFramebufferTexture){if(oe)if(re)t.texStorage2D(i.TEXTURE_2D,He,Q,J.width,J.height);else{let se=J.width,we=J.height;for(let ze=0;ze<He;ze++)t.texImage2D(i.TEXTURE_2D,ze,Q,se,we,0,ce,P,null),se>>=1,we>>=1}}else if(v.isHTMLTexture){if("texElementImage2D"in i){const se=i.canvas;if(se.hasAttribute("layoutsubtree")||se.setAttribute("layoutsubtree","true"),J.parentNode!==se){se.appendChild(J),x.add(v),se.onpaint=we=>{const ze=we.changedElements;for(const ge of x)ze.includes(ge.image)&&(ge.needsUpdate=!0)},se.requestPaint();return}if(i.texElementImage2D.length===3)i.texElementImage2D(i.TEXTURE_2D,i.RGBA8,J);else{const ze=i.RGBA,ge=i.RGBA,rt=i.UNSIGNED_BYTE;i.texElementImage2D(i.TEXTURE_2D,0,ze,ge,rt,J)}i.texParameteri(i.TEXTURE_2D,i.TEXTURE_MIN_FILTER,i.LINEAR),i.texParameteri(i.TEXTURE_2D,i.TEXTURE_WRAP_S,i.CLAMP_TO_EDGE),i.texParameteri(i.TEXTURE_2D,i.TEXTURE_WRAP_T,i.CLAMP_TO_EDGE)}}else if(de.length>0){if(re&&oe){const se=Pe(de[0]);t.texStorage2D(i.TEXTURE_2D,He,Q,se.width,se.height)}for(let se=0,we=de.length;se<we;se++)Y=de[se],re?Te&&t.texSubImage2D(i.TEXTURE_2D,se,0,0,ce,P,Y):t.texImage2D(i.TEXTURE_2D,se,Q,ce,P,Y);v.generateMipmaps=!1}else if(re){if(oe){const se=Pe(J);t.texStorage2D(i.TEXTURE_2D,He,Q,se.width,se.height)}Te&&t.texSubImage2D(i.TEXTURE_2D,0,0,0,ce,P,J)}else t.texImage2D(i.TEXTURE_2D,0,Q,ce,P,J);y(v)&&v.mipmapsAutoUpdate===!0&&E(ie),fe.__version=ae.version,v.onUpdate&&v.onUpdate(v)}w.__version=v.version}function Pt(w,v,B){if(v.image.length!==6)return;const ie=it(w,v),j=v.source;t.bindTexture(i.TEXTURE_CUBE_MAP,w.__webglTexture,i.TEXTURE0+B);const ae=n.get(j);if(j.version!==ae.__version||ie===!0){t.activeTexture(i.TEXTURE0+B);const fe=Ze.getPrimaries(Ze.workingColorSpace),Z=v.colorSpace===Fn?null:Ze.getPrimaries(v.colorSpace),J=v.colorSpace===Fn||fe===Z?i.NONE:i.BROWSER_DEFAULT_WEBGL;t.pixelStorei(i.UNPACK_FLIP_Y_WEBGL,v.flipY),t.pixelStorei(i.UNPACK_PREMULTIPLY_ALPHA_WEBGL,v.premultiplyAlpha),t.pixelStorei(i.UNPACK_ALIGNMENT,v.unpackAlignment),t.pixelStorei(i.UNPACK_COLORSPACE_CONVERSION_WEBGL,J);const ce=v.isCompressedTexture||v.image[0].isCompressedTexture,P=v.image[0]&&v.image[0].isDataTexture,Q=[];for(let ge=0;ge<6;ge++)!ce&&!P?Q[ge]=b(v.image[ge],!0,s.maxCubemapSize):Q[ge]=P?v.image[ge].image:v.image[ge],Q[ge]=Ce(v,Q[ge]);const Y=Q[0],de=r.convert(v.format,v.colorSpace),re=r.convert(v.type);let oe=A(v.internalFormat,de,re,v.normalized,v.colorSpace);const Te=v.isVideoTexture!==!0,He=ae.__version===void 0||ie===!0,se=j.dataReady;let we=M(v,Y);et(i.TEXTURE_CUBE_MAP,v);let ze;if(v.isDepthTexture){if(oe=h(v.format===Bn,v.type),He)if(Te)t.texStorage2D(i.TEXTURE_CUBE_MAP,1,oe,Y.width,Y.height);else for(let ge=0;ge<6;ge++)t.texImage2D(i.TEXTURE_CUBE_MAP_POSITIVE_X+ge,0,oe,Y.width,Y.height,0,de,re,null)}else if(ce){Te&&He&&t.texStorage2D(i.TEXTURE_CUBE_MAP,we,oe,Y.width,Y.height);for(let ge=0;ge<6;ge++){ze=Q[ge].mipmaps;for(let rt=0;rt<ze.length;rt++){const Tt=ze[rt];v.format!==pn?de!==null?Te?se&&t.compressedTexSubImage2D(i.TEXTURE_CUBE_MAP_POSITIVE_X+ge,rt,0,0,Tt.width,Tt.height,de,Tt.data):t.compressedTexImage2D(i.TEXTURE_CUBE_MAP_POSITIVE_X+ge,rt,oe,Tt.width,Tt.height,0,Tt.data):De("WebGLRenderer: Attempt to load unsupported compressed texture format in .setTextureCube()"):Te?se&&t.texSubImage2D(i.TEXTURE_CUBE_MAP_POSITIVE_X+ge,rt,0,0,Tt.width,Tt.height,de,re,Tt.data):t.texImage2D(i.TEXTURE_CUBE_MAP_POSITIVE_X+ge,rt,oe,Tt.width,Tt.height,0,de,re,Tt.data)}}}else{if(ze=v.mipmaps,Te&&He){ze.length>0&&we++;const ge=Pe(Q[0]);t.texStorage2D(i.TEXTURE_CUBE_MAP,we,oe,ge.width,ge.height)}for(let ge=0;ge<6;ge++)if(P){Te?se&&t.texSubImage2D(i.TEXTURE_CUBE_MAP_POSITIVE_X+ge,0,0,0,Q[ge].width,Q[ge].height,de,re,Q[ge].data):t.texImage2D(i.TEXTURE_CUBE_MAP_POSITIVE_X+ge,0,oe,Q[ge].width,Q[ge].height,0,de,re,Q[ge].data);for(let rt=0;rt<ze.length;rt++){const Rn=ze[rt].image[ge].image;Te?se&&t.texSubImage2D(i.TEXTURE_CUBE_MAP_POSITIVE_X+ge,rt+1,0,0,Rn.width,Rn.height,de,re,Rn.data):t.texImage2D(i.TEXTURE_CUBE_MAP_POSITIVE_X+ge,rt+1,oe,Rn.width,Rn.height,0,de,re,Rn.data)}}else{Te?se&&t.texSubImage2D(i.TEXTURE_CUBE_MAP_POSITIVE_X+ge,0,0,0,de,re,Q[ge]):t.texImage2D(i.TEXTURE_CUBE_MAP_POSITIVE_X+ge,0,oe,de,re,Q[ge]);for(let rt=0;rt<ze.length;rt++){const Tt=ze[rt];Te?se&&t.texSubImage2D(i.TEXTURE_CUBE_MAP_POSITIVE_X+ge,rt+1,0,0,de,re,Tt.image[ge]):t.texImage2D(i.TEXTURE_CUBE_MAP_POSITIVE_X+ge,rt+1,oe,de,re,Tt.image[ge])}}}y(v)&&v.mipmapsAutoUpdate===!0&&E(i.TEXTURE_CUBE_MAP),ae.__version=j.version,v.onUpdate&&v.onUpdate(v)}w.__version=v.version}function ct(w,v,B,ie,j,ae){const fe=r.convert(B.format,B.colorSpace),Z=r.convert(B.type),J=A(B.internalFormat,fe,Z,B.normalized,B.colorSpace),ce=n.get(v),P=n.get(B);if(P.__renderTarget=v,!ce.__hasExternalTextures){const Q=Math.max(1,v.width>>ae),Y=Math.max(1,v.height>>ae);j===i.TEXTURE_3D||j===i.TEXTURE_2D_ARRAY?t.texImage3D(j,ae,J,Q,Y,v.depth,0,fe,Z,null):t.texImage2D(j,ae,J,Q,Y,0,fe,Z,null)}t.bindFramebuffer(i.FRAMEBUFFER,w),le(v)?o.framebufferTexture2DMultisampleEXT(i.FRAMEBUFFER,ie,j,P.__webglTexture,0,dt(v)):(j===i.TEXTURE_2D||j>=i.TEXTURE_CUBE_MAP_POSITIVE_X&&j<=i.TEXTURE_CUBE_MAP_NEGATIVE_Z)&&i.framebufferTexture2D(i.FRAMEBUFFER,ie,j,P.__webglTexture,ae),t.bindFramebuffer(i.FRAMEBUFFER,null)}function qt(w,v,B){if(i.bindRenderbuffer(i.RENDERBUFFER,w),v.depthBuffer){const ie=v.depthTexture,j=ie&&ie.isDepthTexture?ie.type:null,ae=h(v.stencilBuffer,j),fe=v.stencilBuffer?i.DEPTH_STENCIL_ATTACHMENT:i.DEPTH_ATTACHMENT;le(v)?o.renderbufferStorageMultisampleEXT(i.RENDERBUFFER,dt(v),ae,v.width,v.height):B?i.renderbufferStorageMultisample(i.RENDERBUFFER,dt(v),ae,v.width,v.height):i.renderbufferStorage(i.RENDERBUFFER,ae,v.width,v.height),i.framebufferRenderbuffer(i.FRAMEBUFFER,fe,i.RENDERBUFFER,w)}else{const ie=v.textures;for(let j=0;j<ie.length;j++){const ae=ie[j],fe=r.convert(ae.format,ae.colorSpace),Z=r.convert(ae.type),J=A(ae.internalFormat,fe,Z,ae.normalized,ae.colorSpace);le(v)?o.renderbufferStorageMultisampleEXT(i.RENDERBUFFER,dt(v),J,v.width,v.height):B?i.renderbufferStorageMultisample(i.RENDERBUFFER,dt(v),J,v.width,v.height):i.renderbufferStorage(i.RENDERBUFFER,J,v.width,v.height)}}i.bindRenderbuffer(i.RENDERBUFFER,null)}function vt(w,v,B){const ie=v.isWebGLCubeRenderTarget===!0;if(t.bindFramebuffer(i.FRAMEBUFFER,w),!(v.depthTexture&&v.depthTexture.isDepthTexture))throw new Error("THREE.WebGLTextures: renderTarget.depthTexture must be an instance of THREE.DepthTexture.");const j=n.get(v.depthTexture);j.__renderTarget=v;const ae=ie?v.depthTexture.image[0]:v.depthTexture.image;(!j.__webglTexture||ae.width!==v.width||ae.height!==v.height)&&(ae.width=v.width,ae.height=v.height,v.depthTexture.needsUpdate=!0),ie?me(v.depthTexture,0):pe(v.depthTexture,0);const fe=j.__webglTexture,Z=dt(v),J=ie?i.TEXTURE_CUBE_MAP_POSITIVE_X+B:i.TEXTURE_2D,ce=v.depthTexture.format===Bn?i.DEPTH_STENCIL_ATTACHMENT:i.DEPTH_ATTACHMENT;if(v.depthTexture.format===zn)le(v)?o.framebufferTexture2DMultisampleEXT(i.FRAMEBUFFER,ce,J,fe,0,Z):i.framebufferTexture2D(i.FRAMEBUFFER,ce,J,fe,0);else if(v.depthTexture.format===Bn)le(v)?o.framebufferTexture2DMultisampleEXT(i.FRAMEBUFFER,ce,J,fe,0,Z):i.framebufferTexture2D(i.FRAMEBUFFER,ce,J,fe,0);else throw new Error("THREE.WebGLTextures: Unknown depthTexture format.")}function Ft(w){const v=n.get(w),B=w.isWebGLCubeRenderTarget===!0;if(v.__boundDepthTexture!==w.depthTexture){const ie=w.depthTexture;if(v.__depthDisposeCallback&&v.__depthDisposeCallback(),ie){const j=()=>{delete v.__boundDepthTexture,delete v.__depthDisposeCallback,ie.removeEventListener("dispose",j)};ie.addEventListener("dispose",j),v.__depthDisposeCallback=j}v.__boundDepthTexture=ie}if(w.depthTexture&&!v.__autoAllocateDepthBuffer)if(B)for(let ie=0;ie<6;ie++)vt(v.__webglFramebuffer[ie],w,ie);else w.texture.mipmaps.length>0?vt(v.__webglFramebuffer[0],w,0):vt(v.__webglFramebuffer,w,0);else if(B){v.__webglDepthbuffer=[];for(let ie=0;ie<6;ie++)if(t.bindFramebuffer(i.FRAMEBUFFER,v.__webglFramebuffer[ie]),v.__webglDepthbuffer[ie]===void 0)v.__webglDepthbuffer[ie]=i.createRenderbuffer(),qt(v.__webglDepthbuffer[ie],w,!1);else{const j=w.stencilBuffer?i.DEPTH_STENCIL_ATTACHMENT:i.DEPTH_ATTACHMENT,ae=v.__webglDepthbuffer[ie];i.bindRenderbuffer(i.RENDERBUFFER,ae),i.framebufferRenderbuffer(i.FRAMEBUFFER,j,i.RENDERBUFFER,ae)}}else if(w.texture.mipmaps.length>0?t.bindFramebuffer(i.FRAMEBUFFER,v.__webglFramebuffer[0]):t.bindFramebuffer(i.FRAMEBUFFER,v.__webglFramebuffer),v.__webglDepthbuffer===void 0)v.__webglDepthbuffer=i.createRenderbuffer(),qt(v.__webglDepthbuffer,w,!1);else{const j=w.stencilBuffer?i.DEPTH_STENCIL_ATTACHMENT:i.DEPTH_ATTACHMENT,ae=v.__webglDepthbuffer;i.bindRenderbuffer(i.RENDERBUFFER,ae),i.framebufferRenderbuffer(i.FRAMEBUFFER,j,i.RENDERBUFFER,ae)}t.bindFramebuffer(i.FRAMEBUFFER,null)}function Ot(w,v,B){const ie=n.get(w);v!==void 0&&ct(ie.__webglFramebuffer,w,w.texture,i.COLOR_ATTACHMENT0,i.TEXTURE_2D,0),B!==void 0&&Ft(w)}function Gt(w){const v=w.texture,B=n.get(w),ie=n.get(v);w.addEventListener("dispose",F),I(w);const j=w.textures,ae=w.isWebGLCubeRenderTarget===!0,fe=j.length>1;if(fe||(ie.__webglTexture===void 0&&(ie.__webglTexture=i.createTexture()),ie.__version=v.version,a.memory.textures++),ae){B.__webglFramebuffer=[];for(let Z=0;Z<6;Z++)if(v.mipmaps.length>0){B.__webglFramebuffer[Z]=[];for(let J=0;J<v.mipmaps.length;J++)B.__webglFramebuffer[Z][J]=i.createFramebuffer()}else B.__webglFramebuffer[Z]=i.createFramebuffer()}else{if(v.mipmaps.length>0){B.__webglFramebuffer=[];for(let Z=0;Z<v.mipmaps.length;Z++)B.__webglFramebuffer[Z]=i.createFramebuffer()}else B.__webglFramebuffer=i.createFramebuffer();if(fe)for(let Z=0,J=j.length;Z<J;Z++){const ce=n.get(j[Z]);ce.__webglTexture===void 0&&(ce.__webglTexture=i.createTexture(),a.memory.textures++)}if(w.samples>0&&le(w)===!1){B.__webglMultisampledFramebuffer=i.createFramebuffer(),B.__webglColorRenderbuffer=[],t.bindFramebuffer(i.FRAMEBUFFER,B.__webglMultisampledFramebuffer);for(let Z=0;Z<j.length;Z++){const J=j[Z];B.__webglColorRenderbuffer[Z]=i.createRenderbuffer(),i.bindRenderbuffer(i.RENDERBUFFER,B.__webglColorRenderbuffer[Z]);const ce=r.convert(J.format,J.colorSpace),P=r.convert(J.type),Q=A(J.internalFormat,ce,P,J.normalized,J.colorSpace,w.isXRRenderTarget===!0),Y=dt(w);i.renderbufferStorageMultisample(i.RENDERBUFFER,Y,Q,w.width,w.height),i.framebufferRenderbuffer(i.FRAMEBUFFER,i.COLOR_ATTACHMENT0+Z,i.RENDERBUFFER,B.__webglColorRenderbuffer[Z])}i.bindRenderbuffer(i.RENDERBUFFER,null),w.depthBuffer&&(B.__webglDepthRenderbuffer=i.createRenderbuffer(),qt(B.__webglDepthRenderbuffer,w,!0)),t.bindFramebuffer(i.FRAMEBUFFER,null)}}if(ae){t.bindTexture(i.TEXTURE_CUBE_MAP,ie.__webglTexture),et(i.TEXTURE_CUBE_MAP,v);for(let Z=0;Z<6;Z++)if(v.mipmaps.length>0)for(let J=0;J<v.mipmaps.length;J++)ct(B.__webglFramebuffer[Z][J],w,v,i.COLOR_ATTACHMENT0,i.TEXTURE_CUBE_MAP_POSITIVE_X+Z,J);else ct(B.__webglFramebuffer[Z],w,v,i.COLOR_ATTACHMENT0,i.TEXTURE_CUBE_MAP_POSITIVE_X+Z,0);y(v)?E(i.TEXTURE_CUBE_MAP):v.mipmaps.length>0&&i.texParameteri(i.TEXTURE_CUBE_MAP,i.TEXTURE_MAX_LEVEL,v.mipmaps.length-1),t.unbindTexture()}else if(fe){for(let Z=0,J=j.length;Z<J;Z++){const ce=j[Z],P=n.get(ce);let Q=i.TEXTURE_2D;(w.isWebGL3DRenderTarget||w.isWebGLArrayRenderTarget)&&(Q=w.isWebGL3DRenderTarget?i.TEXTURE_3D:i.TEXTURE_2D_ARRAY),t.bindTexture(Q,P.__webglTexture),et(Q,ce),ct(B.__webglFramebuffer,w,ce,i.COLOR_ATTACHMENT0+Z,Q,0),y(ce)&&E(Q)}t.unbindTexture()}else{let Z=i.TEXTURE_2D;if((w.isWebGL3DRenderTarget||w.isWebGLArrayRenderTarget)&&(Z=w.isWebGL3DRenderTarget?i.TEXTURE_3D:i.TEXTURE_2D_ARRAY),t.bindTexture(Z,ie.__webglTexture),et(Z,v),v.mipmaps.length>0)for(let J=0;J<v.mipmaps.length;J++)ct(B.__webglFramebuffer[J],w,v,i.COLOR_ATTACHMENT0,Z,J);else ct(B.__webglFramebuffer,w,v,i.COLOR_ATTACHMENT0,Z,0);y(v)?E(Z):v.mipmaps.length>0&&i.texParameteri(Z,i.TEXTURE_MAX_LEVEL,v.mipmaps.length-1),t.unbindTexture()}w.depthBuffer&&Ft(w)}function N(w){const v=w.textures;for(let B=0,ie=v.length;B<ie;B++){const j=v[B];if(y(j)&&j.mipmapsAutoUpdate===!0){const ae=R(w),fe=n.get(j).__webglTexture;t.bindTexture(ae,fe),E(ae),t.unbindTexture()}}}const Ht=[];function ot(w){if(w.samples>0){if(le(w)===!1){const v=w.textures,B=w.width,ie=w.height;let j=i.COLOR_BUFFER_BIT;const ae=w.stencilBuffer?i.DEPTH_STENCIL_ATTACHMENT:i.DEPTH_ATTACHMENT,fe=n.get(w),Z=v.length>1;if(Z)for(let ce=0;ce<v.length;ce++)t.bindFramebuffer(i.FRAMEBUFFER,fe.__webglMultisampledFramebuffer),i.framebufferRenderbuffer(i.FRAMEBUFFER,i.COLOR_ATTACHMENT0+ce,i.RENDERBUFFER,null),t.bindFramebuffer(i.FRAMEBUFFER,fe.__webglFramebuffer),i.framebufferTexture2D(i.DRAW_FRAMEBUFFER,i.COLOR_ATTACHMENT0+ce,i.TEXTURE_2D,null,0);t.bindFramebuffer(i.READ_FRAMEBUFFER,fe.__webglMultisampledFramebuffer),w.texture.mipmaps.length>0?t.bindFramebuffer(i.DRAW_FRAMEBUFFER,fe.__webglFramebuffer[0]):t.bindFramebuffer(i.DRAW_FRAMEBUFFER,fe.__webglFramebuffer);for(let ce=0;ce<v.length;ce++){if(w.resolveDepthBuffer&&(w.depthBuffer&&(j|=i.DEPTH_BUFFER_BIT),w.stencilBuffer&&w.resolveStencilBuffer&&(j|=i.STENCIL_BUFFER_BIT)),Z){i.framebufferRenderbuffer(i.READ_FRAMEBUFFER,i.COLOR_ATTACHMENT0,i.RENDERBUFFER,fe.__webglColorRenderbuffer[ce]);const P=n.get(v[ce]).__webglTexture;i.framebufferTexture2D(i.DRAW_FRAMEBUFFER,i.COLOR_ATTACHMENT0,i.TEXTURE_2D,P,0)}i.blitFramebuffer(0,0,B,ie,0,0,B,ie,j,i.NEAREST),d===!0&&(Ht.length=0,Ht.push(i.COLOR_ATTACHMENT0+ce),w.depthBuffer&&w.storeMultisampledDepthBuffer===!1&&Ht.push(ae),i.invalidateFramebuffer(i.READ_FRAMEBUFFER,Ht))}if(t.bindFramebuffer(i.READ_FRAMEBUFFER,null),t.bindFramebuffer(i.DRAW_FRAMEBUFFER,null),Z)for(let ce=0;ce<v.length;ce++){t.bindFramebuffer(i.FRAMEBUFFER,fe.__webglMultisampledFramebuffer),i.framebufferRenderbuffer(i.FRAMEBUFFER,i.COLOR_ATTACHMENT0+ce,i.RENDERBUFFER,fe.__webglColorRenderbuffer[ce]);const P=n.get(v[ce]).__webglTexture;t.bindFramebuffer(i.FRAMEBUFFER,fe.__webglFramebuffer),i.framebufferTexture2D(i.DRAW_FRAMEBUFFER,i.COLOR_ATTACHMENT0+ce,i.TEXTURE_2D,P,0)}t.bindFramebuffer(i.DRAW_FRAMEBUFFER,fe.__webglMultisampledFramebuffer)}else if(w.depthBuffer&&w.storeMultisampledDepthBuffer===!1&&d){const v=w.stencilBuffer?i.DEPTH_STENCIL_ATTACHMENT:i.DEPTH_ATTACHMENT;i.invalidateFramebuffer(i.DRAW_FRAMEBUFFER,[v])}}}function dt(w){return Math.min(s.maxSamples,w.samples)}function le(w){const v=n.get(w);return w.samples>0&&e.has("WEBGL_multisampled_render_to_texture")===!0&&v.__useRenderToTexture!==!1}function st(w){const v=a.render.frame;m.get(w)!==v&&(m.set(w,v),w.update())}function Ce(w,v){const B=w.colorSpace,ie=w.format,j=w.type;return w.isCompressedTexture===!0||w.isVideoTexture===!0||B!==Ms&&B!==Fn&&(Ze.getTransfer(B)===at?(ie!==pn||j!==Zt)&&De("WebGLTextures: sRGB encoded textures have to use RGBAFormat and UnsignedByteType."):nt("WebGLTextures: Unsupported texture color space:",B)),v}function Pe(w){return typeof HTMLImageElement<"u"&&w instanceof HTMLImageElement?(c.width=w.naturalWidth||w.width,c.height=w.naturalHeight||w.height):typeof VideoFrame<"u"&&w instanceof VideoFrame?(c.width=w.displayWidth,c.height=w.displayHeight):(c.width=w.width,c.height=w.height),c}this.allocateTextureUnit=Le,this.resetTextureUnits=ee,this.getTextureUnits=te,this.setTextureUnits=Me,this.setTexture2D=pe,this.setTexture2DArray=ve,this.setTexture3D=Ae,this.setTextureCube=me,this.rebindTextures=Ot,this.setupRenderTarget=Gt,this.updateRenderTargetMipmap=N,this.updateMultisampleRenderTarget=ot,this.setupDepthRenderbuffer=Ft,this.setupFrameBufferTexture=ct,this.useMultisampledRTT=le,this.dispose=W}function Bp(i,e){function t(n,s=Fn){let r;const a=Ze.getTransfer(s);if(n===Zt)return i.UNSIGNED_BYTE;if(n===ta)return i.UNSIGNED_SHORT_4_4_4_4;if(n===na)return i.UNSIGNED_SHORT_5_5_5_1;if(n===Go)return i.UNSIGNED_INT_5_9_9_9_REV;if(n===Ho)return i.UNSIGNED_INT_10F_11F_11F_REV;if(n===ko)return i.BYTE;if(n===zo)return i.SHORT;if(n===Ni)return i.UNSIGNED_SHORT;if(n===ea)return i.INT;if(n===gn)return i.UNSIGNED_INT;if(n===hn)return i.FLOAT;if(n===xn)return i.HALF_FLOAT;if(n===Vo)return i.ALPHA;if(n===Wo)return i.RGB;if(n===pn)return i.RGBA;if(n===zn)return i.DEPTH_COMPONENT;if(n===Bn)return i.DEPTH_STENCIL;if(n===Xo)return i.RED;if(n===ia)return i.RED_INTEGER;if(n===Qn)return i.RG;if(n===sa)return i.RG_INTEGER;if(n===ra)return i.RGBA_INTEGER;if(n===hs||n===ps||n===ms||n===gs)if(a===at)if(r=e.get("WEBGL_compressed_texture_s3tc_srgb"),r!==null){if(n===hs)return r.COMPRESSED_SRGB_S3TC_DXT1_EXT;if(n===ps)return r.COMPRESSED_SRGB_ALPHA_S3TC_DXT1_EXT;if(n===ms)return r.COMPRESSED_SRGB_ALPHA_S3TC_DXT3_EXT;if(n===gs)return r.COMPRESSED_SRGB_ALPHA_S3TC_DXT5_EXT}else return null;else if(r=e.get("WEBGL_compressed_texture_s3tc"),r!==null){if(n===hs)return r.COMPRESSED_RGB_S3TC_DXT1_EXT;if(n===ps)return r.COMPRESSED_RGBA_S3TC_DXT1_EXT;if(n===ms)return r.COMPRESSED_RGBA_S3TC_DXT3_EXT;if(n===gs)return r.COMPRESSED_RGBA_S3TC_DXT5_EXT}else return null;if(n===_r||n===vr||n===Sr||n===Mr)if(r=e.get("WEBGL_compressed_texture_pvrtc"),r!==null){if(n===_r)return r.COMPRESSED_RGB_PVRTC_4BPPV1_IMG;if(n===vr)return r.COMPRESSED_RGB_PVRTC_2BPPV1_IMG;if(n===Sr)return r.COMPRESSED_RGBA_PVRTC_4BPPV1_IMG;if(n===Mr)return r.COMPRESSED_RGBA_PVRTC_2BPPV1_IMG}else return null;if(n===br||n===yr||n===Er||n===Tr||n===wr||n===vs||n===Ar)if(r=e.get("WEBGL_compressed_texture_etc"),r!==null){if(n===br||n===yr)return a===at?r.COMPRESSED_SRGB8_ETC2:r.COMPRESSED_RGB8_ETC2;if(n===Er)return a===at?r.COMPRESSED_SRGB8_ALPHA8_ETC2_EAC:r.COMPRESSED_RGBA8_ETC2_EAC;if(n===Tr)return r.COMPRESSED_R11_EAC;if(n===wr)return r.COMPRESSED_SIGNED_R11_EAC;if(n===vs)return r.COMPRESSED_RG11_EAC;if(n===Ar)return r.COMPRESSED_SIGNED_RG11_EAC}else return null;if(n===Rr||n===Cr||n===Pr||n===Lr||n===Dr||n===Ir||n===Nr||n===Ur||n===Fr||n===Or||n===Br||n===kr||n===zr||n===Gr)if(r=e.get("WEBGL_compressed_texture_astc"),r!==null){if(n===Rr)return a===at?r.COMPRESSED_SRGB8_ALPHA8_ASTC_4x4_KHR:r.COMPRESSED_RGBA_ASTC_4x4_KHR;if(n===Cr)return a===at?r.COMPRESSED_SRGB8_ALPHA8_ASTC_5x4_KHR:r.COMPRESSED_RGBA_ASTC_5x4_KHR;if(n===Pr)return a===at?r.COMPRESSED_SRGB8_ALPHA8_ASTC_5x5_KHR:r.COMPRESSED_RGBA_ASTC_5x5_KHR;if(n===Lr)return a===at?r.COMPRESSED_SRGB8_ALPHA8_ASTC_6x5_KHR:r.COMPRESSED_RGBA_ASTC_6x5_KHR;if(n===Dr)return a===at?r.COMPRESSED_SRGB8_ALPHA8_ASTC_6x6_KHR:r.COMPRESSED_RGBA_ASTC_6x6_KHR;if(n===Ir)return a===at?r.COMPRESSED_SRGB8_ALPHA8_ASTC_8x5_KHR:r.COMPRESSED_RGBA_ASTC_8x5_KHR;if(n===Nr)return a===at?r.COMPRESSED_SRGB8_ALPHA8_ASTC_8x6_KHR:r.COMPRESSED_RGBA_ASTC_8x6_KHR;if(n===Ur)return a===at?r.COMPRESSED_SRGB8_ALPHA8_ASTC_8x8_KHR:r.COMPRESSED_RGBA_ASTC_8x8_KHR;if(n===Fr)return a===at?r.COMPRESSED_SRGB8_ALPHA8_ASTC_10x5_KHR:r.COMPRESSED_RGBA_ASTC_10x5_KHR;if(n===Or)return a===at?r.COMPRESSED_SRGB8_ALPHA8_ASTC_10x6_KHR:r.COMPRESSED_RGBA_ASTC_10x6_KHR;if(n===Br)return a===at?r.COMPRESSED_SRGB8_ALPHA8_ASTC_10x8_KHR:r.COMPRESSED_RGBA_ASTC_10x8_KHR;if(n===kr)return a===at?r.COMPRESSED_SRGB8_ALPHA8_ASTC_10x10_KHR:r.COMPRESSED_RGBA_ASTC_10x10_KHR;if(n===zr)return a===at?r.COMPRESSED_SRGB8_ALPHA8_ASTC_12x10_KHR:r.COMPRESSED_RGBA_ASTC_12x10_KHR;if(n===Gr)return a===at?r.COMPRESSED_SRGB8_ALPHA8_ASTC_12x12_KHR:r.COMPRESSED_RGBA_ASTC_12x12_KHR}else return null;if(n===Hr||n===Vr||n===Wr)if(r=e.get("EXT_texture_compression_bptc"),r!==null){if(n===Hr)return a===at?r.COMPRESSED_SRGB_ALPHA_BPTC_UNORM_EXT:r.COMPRESSED_RGBA_BPTC_UNORM_EXT;if(n===Vr)return r.COMPRESSED_RGB_BPTC_SIGNED_FLOAT_EXT;if(n===Wr)return r.COMPRESSED_RGB_BPTC_UNSIGNED_FLOAT_EXT}else return null;if(n===Xr||n===qr||n===Ss||n===Yr)if(r=e.get("EXT_texture_compression_rgtc"),r!==null){if(n===Xr)return r.COMPRESSED_RED_RGTC1_EXT;if(n===qr)return r.COMPRESSED_SIGNED_RED_RGTC1_EXT;if(n===Ss)return r.COMPRESSED_RED_GREEN_RGTC2_EXT;if(n===Yr)return r.COMPRESSED_SIGNED_RED_GREEN_RGTC2_EXT}else return null;return n===_i?i.UNSIGNED_INT_24_8:i[n]!==void 0?i[n]:null}return{convert:t}}const kp=`
void main() {

	gl_Position = vec4( position, 1.0 );

}`,zp=`
uniform sampler2DArray depthColor;
uniform float depthWidth;
uniform float depthHeight;

void main() {

	vec2 coord = vec2( gl_FragCoord.x / depthWidth, gl_FragCoord.y / depthHeight );

	if ( coord.x >= 1.0 ) {

		gl_FragDepth = texture( depthColor, vec3( coord.x - 1.0, coord.y, 1 ) ).r;

	} else {

		gl_FragDepth = texture( depthColor, vec3( coord.x, coord.y, 0 ) ).r;

	}

}`;class Gp{constructor(){this.texture=null,this.mesh=null,this.depthNear=0,this.depthFar=0}init(e,t){if(this.texture===null){const n=new il(e.texture);(e.depthNear!==t.depthNear||e.depthFar!==t.depthFar)&&(this.depthNear=e.depthNear,this.depthFar=e.depthFar),this.texture=n}}getMesh(e){if(this.texture!==null&&this.mesh===null){const t=e.cameras[0].viewport,n=new jn({vertexShader:kp,fragmentShader:zp,uniforms:{depthColor:{value:this.texture},depthWidth:{value:t.z},depthHeight:{value:t.w}}});this.mesh=new ht(new Cs(20,20),n)}return this.mesh}reset(){this.texture=null,this.mesh=null}getDepthTexture(){return this.texture}}class Hp extends ei{constructor(e,t){super();const n=this;let s=null,r=1,a=null,o="local-floor",d=1,c=null,m=null,x=null,f=null,g=null,l=null;const T=typeof XRWebGLBinding<"u",p=new Gp,u={},b=t.getContextAttributes();let y=null,E=null;const R=[],A=[],h=new Xe;let M=null,I=null;const C=new Qt;C.viewport=new pt;const U=new Qt;U.viewport=new pt;const F=[C,U],X=new Qc;let L=null,W=null;this.cameraAutoUpdate=!0,this.enabled=!1,this.isPresenting=!1,this.getController=function($){let pe=R[$];return pe===void 0&&(pe=new Xs,R[$]=pe),pe.getTargetRaySpace()},this.getControllerGrip=function($){let pe=R[$];return pe===void 0&&(pe=new Xs,R[$]=pe),pe.getGripSpace()},this.getHand=function($){let pe=R[$];return pe===void 0&&(pe=new Xs,R[$]=pe),pe.getHandSpace()};function K($){const pe=A.indexOf($.inputSource);if(pe===-1)return;const ve=R[pe];ve!==void 0&&(ve.update($.inputSource,$.frame,c||a),ve.dispatchEvent({type:$.type,data:$.inputSource}))}function H(){s.removeEventListener("select",K),s.removeEventListener("selectstart",K),s.removeEventListener("selectend",K),s.removeEventListener("squeeze",K),s.removeEventListener("squeezestart",K),s.removeEventListener("squeezeend",K),s.removeEventListener("end",H),s.removeEventListener("inputsourceschange",ne);for(let $=0;$<R.length;$++){const pe=A[$];pe!==null&&(A[$]=null,R[$].disconnect(pe))}L=null,W=null,p.reset();for(const $ in u)delete u[$];if(e.setRenderTarget(y),g=null,f=null,x=null,s=null,E=null,Le.stop(),n.isPresenting=!1,e.setPixelRatio(M),e.setSize(h.width,h.height,!1),I!==null){const $=I.camera;$.fov=I.fov,$.zoom=I.zoom,$.updateProjectionMatrix(),I=null}n.dispatchEvent({type:"sessionend"})}this.setFramebufferScaleFactor=function($){r=$,n.isPresenting===!0&&De("WebXRManager: Cannot change framebuffer scale while presenting.")},this.setReferenceSpaceType=function($){o=$,n.isPresenting===!0&&De("WebXRManager: Cannot change reference space type while presenting.")},this.getReferenceSpace=function(){return c||a},this.setReferenceSpace=function($){c=$},this.getBaseLayer=function(){return f!==null?f:g},this.getBinding=function(){return x===null&&T&&(x=new XRWebGLBinding(s,t)),x},this.getFrame=function(){return l},this.getSession=function(){return s},this.setSession=async function($){if(s=$,s!==null){if(y=e.getRenderTarget(),s.addEventListener("select",K),s.addEventListener("selectstart",K),s.addEventListener("selectend",K),s.addEventListener("squeeze",K),s.addEventListener("squeezestart",K),s.addEventListener("squeezeend",K),s.addEventListener("end",H),s.addEventListener("inputsourceschange",ne),b.xrCompatible!==!0&&await t.makeXRCompatible(),M=e.getPixelRatio(),e.getSize(h),T&&"createProjectionLayer"in XRWebGLBinding.prototype){let ve=null,Ae=null,me=null;b.depth&&(me=b.stencil?t.DEPTH24_STENCIL8:t.DEPTH_COMPONENT24,ve=b.stencil?Bn:zn,Ae=b.stencil?_i:gn);const Ie={colorFormat:t.RGBA8,depthFormat:me,scaleFactor:r};x=this.getBinding(),f=x.createProjectionLayer(Ie),s.updateRenderState({layers:[f]}),e.setPixelRatio(1),e.setSize(f.textureWidth,f.textureHeight,!1),E=new mn(f.textureWidth,f.textureHeight,{format:pn,type:Zt,depthTexture:new vi(f.textureWidth,f.textureHeight,Ae,void 0,void 0,void 0,void 0,void 0,void 0,ve),stencilBuffer:b.stencil,colorSpace:e.outputColorSpace,samples:b.antialias?4:0,resolveDepthBuffer:f.ignoreDepthValues===!1,resolveStencilBuffer:f.ignoreDepthValues===!1,storeMultisampledDepthBuffer:f.ignoreDepthValues===!1,storeMultisampledStencilBuffer:f.ignoreDepthValues===!1})}else{const ve={antialias:b.antialias,alpha:!0,depth:b.depth,stencil:b.stencil,framebufferScaleFactor:r};g=new XRWebGLLayer(s,t,ve),s.updateRenderState({baseLayer:g}),e.setPixelRatio(1),e.setSize(g.framebufferWidth,g.framebufferHeight,!1),E=new mn(g.framebufferWidth,g.framebufferHeight,{format:pn,type:Zt,colorSpace:e.outputColorSpace,stencilBuffer:b.stencil,resolveDepthBuffer:g.ignoreDepthValues===!1,resolveStencilBuffer:g.ignoreDepthValues===!1,storeMultisampledDepthBuffer:g.ignoreDepthValues===!1,storeMultisampledStencilBuffer:g.ignoreDepthValues===!1})}E.isXRRenderTarget=!0,this.setFoveation(d),c=null,a=await s.requestReferenceSpace(o),Le.setContext(s),Le.start(),n.isPresenting=!0,n.dispatchEvent({type:"sessionstart"})}},this.getEnvironmentBlendMode=function(){if(s!==null)return s.environmentBlendMode},this.getDepthTexture=function(){return p.getDepthTexture()};function ne($){for(let pe=0;pe<$.removed.length;pe++){const ve=$.removed[pe],Ae=A.indexOf(ve);Ae>=0&&(A[Ae]=null,R[Ae].disconnect(ve))}for(let pe=0;pe<$.added.length;pe++){const ve=$.added[pe];let Ae=A.indexOf(ve);if(Ae===-1){for(let Ie=0;Ie<R.length;Ie++)if(Ie>=A.length){A.push(ve),Ae=Ie;break}else if(A[Ie]===null){A[Ie]=ve,Ae=Ie;break}if(Ae===-1)break}const me=R[Ae];me&&me.connect(ve)}}function q($,pe){pe===null?$.matrixWorld.copy($.matrix):$.matrixWorld.multiplyMatrices(pe.matrixWorld,$.matrix),$.matrixWorldInverse.copy($.matrixWorld).invert()}this.updateCamera=function($){if(s===null)return;let pe=$.near,ve=$.far;p.texture!==null&&(p.depthNear>0&&(pe=p.depthNear),p.depthFar>0&&(ve=p.depthFar)),X.near=U.near=C.near=pe,X.far=U.far=C.far=ve,(L!==X.near||W!==X.far)&&(s.updateRenderState({depthNear:X.near,depthFar:X.far}),L=X.near,W=X.far),X.layers.mask=$.layers.mask|6,C.layers.mask=X.layers.mask&-5,U.layers.mask=X.layers.mask&-3;const Ae=$.parent,me=X.cameras;q(X,Ae);for(let Ie=0;Ie<me.length;Ie++)q(me[Ie],Ae);X.projectionMatrix.copy(C.projectionMatrix),X.projectionMatrixInverse.copy(C.projectionMatrixInverse),I===null&&$.isPerspectiveCamera&&(I={camera:$,fov:$.fov,zoom:$.zoom}),ee($,X,Ae)};function ee($,pe,ve){ve===null?$.matrix.copy(pe.matrixWorld):($.matrix.copy(ve.matrixWorld),$.matrix.invert(),$.matrix.multiply(pe.matrixWorld)),$.matrix.decompose($.position,$.quaternion,$.scale),$.updateMatrixWorld(!0),$.projectionMatrix.copy(pe.projectionMatrix),$.projectionMatrixInverse.copy(pe.projectionMatrixInverse),$.isPerspectiveCamera&&($.fov=Zr*2*Math.atan(1/$.projectionMatrix.elements[5]),$.zoom=1)}this.getCamera=function(){return X},this.getFoveation=function(){if(!(f===null&&g===null))return d},this.setFoveation=function($){d=$,f!==null&&(f.fixedFoveation=$),g!==null&&g.fixedFoveation!==void 0&&(g.fixedFoveation=$)},this.hasDepthSensing=function(){return p.texture!==null},this.getDepthSensingMesh=function(){return p.getMesh(X)},this.getCameraTexture=function($){return u[$]};let te=null;function Me($,pe){if(m=pe.getViewerPose(c||a),l=pe,m!==null){const ve=m.views;g!==null&&(e.setRenderTargetFramebuffer(E,g.framebuffer),e.setRenderTarget(E));let Ae=!1;ve.length!==X.cameras.length&&(X.cameras.length=0,Ae=!0);for(let Re=0;Re<ve.length;Re++){const et=ve[Re];let it=null;if(g!==null)it=g.getViewport(et);else{const bt=x.getViewSubImage(f,et);it=bt.viewport,Re===0&&(e.setRenderTargetTextures(E,bt.colorTexture,bt.depthStencilTexture),e.setRenderTarget(E))}let Ge=F[Re];Ge===void 0&&(Ge=new Qt,Ge.layers.enable(Re),Ge.viewport=new pt,F[Re]=Ge),Ge.matrix.fromArray(et.transform.matrix),Ge.matrix.decompose(Ge.position,Ge.quaternion,Ge.scale),Ge.projectionMatrix.fromArray(et.projectionMatrix),Ge.projectionMatrixInverse.copy(Ge.projectionMatrix).invert(),Ge.viewport.set(it.x,it.y,it.width,it.height),Re===0&&(X.matrix.copy(Ge.matrix),X.matrix.decompose(X.position,X.quaternion,X.scale)),Ae===!0&&X.cameras.push(Ge)}const me=s.enabledFeatures;if(me&&me.includes("depth-sensing")&&s.depthUsage=="gpu-optimized"&&T){x=n.getBinding();const Re=x.getDepthInformation(ve[0]);Re&&Re.isValid&&Re.texture&&p.init(Re,s.renderState)}if(me&&me.includes("camera-access")&&T){e.state.unbindTexture(),x=n.getBinding();for(let Re=0;Re<ve.length;Re++){const et=ve[Re].camera;if(et){let it=u[et];it||(it=new il,u[et]=it);const Ge=x.getCameraImage(et);it.sourceTexture=Ge}}}}for(let ve=0;ve<R.length;ve++){const Ae=A[ve],me=R[ve];Ae!==null&&me!==void 0&&me.update(Ae,pe,c||a)}te&&te($,pe),pe.detectedPlanes&&n.dispatchEvent({type:"planesdetected",data:pe}),l=null}const Le=new ll;Le.setAnimationLoop(Me),this.setAnimationLoop=function($){te=$},this.dispose=function(){}}}const Vp=new mt,_l=new Ne;_l.set(-1,0,0,0,1,0,0,0,1);function Wp(i,e){function t(p,u){p.matrixAutoUpdate===!0&&p.updateMatrix(),u.value.copy(p.matrix)}function n(p,u){u.color.getRGB(p.fogColor.value,sl(i)),u.isFog?(p.fogNear.value=u.near,p.fogFar.value=u.far):u.isFogExp2&&(p.fogDensity.value=u.density)}function s(p,u,b,y,E){u.isNodeMaterial?u.uniformsNeedUpdate=!1:u.isMeshBasicMaterial?r(p,u):u.isMeshLambertMaterial?(r(p,u),u.envMap&&(p.envMapIntensity.value=u.envMapIntensity)):u.isMeshToonMaterial?(r(p,u),x(p,u)):u.isMeshPhongMaterial?(r(p,u),m(p,u),u.envMap&&(p.envMapIntensity.value=u.envMapIntensity)):u.isMeshStandardMaterial?(r(p,u),f(p,u),u.isMeshPhysicalMaterial&&g(p,u,E)):u.isMeshMatcapMaterial?(r(p,u),l(p,u)):u.isMeshDepthMaterial?r(p,u):u.isMeshDistanceMaterial?(r(p,u),T(p,u)):u.isMeshNormalMaterial?r(p,u):u.isLineBasicMaterial?(a(p,u),u.isLineDashedMaterial&&o(p,u)):u.isPointsMaterial?d(p,u,b,y):u.isSpriteMaterial?c(p,u):u.isShadowMaterial?(p.color.value.copy(u.color),p.opacity.value=u.opacity):u.isShaderMaterial&&(u.uniformsNeedUpdate=!1)}function r(p,u){p.opacity.value=u.opacity,u.color&&p.diffuse.value.copy(u.color),u.emissive&&p.emissive.value.copy(u.emissive).multiplyScalar(u.emissiveIntensity),u.map&&(p.map.value=u.map,t(u.map,p.mapTransform)),u.alphaMap&&(p.alphaMap.value=u.alphaMap,t(u.alphaMap,p.alphaMapTransform)),u.bumpMap&&(p.bumpMap.value=u.bumpMap,t(u.bumpMap,p.bumpMapTransform),p.bumpScale.value=u.bumpScale,u.side===Xt&&(p.bumpScale.value*=-1)),u.normalMap&&(p.normalMap.value=u.normalMap,t(u.normalMap,p.normalMapTransform),p.normalScale.value.copy(u.normalScale),u.side===Xt&&p.normalScale.value.negate()),u.displacementMap&&(p.displacementMap.value=u.displacementMap,t(u.displacementMap,p.displacementMapTransform),p.displacementScale.value=u.displacementScale,p.displacementBias.value=u.displacementBias),u.emissiveMap&&(p.emissiveMap.value=u.emissiveMap,t(u.emissiveMap,p.emissiveMapTransform)),u.specularMap&&(p.specularMap.value=u.specularMap,t(u.specularMap,p.specularMapTransform)),u.alphaTest>0&&(p.alphaTest.value=u.alphaTest);const b=e.get(u),y=b.envMap,E=b.envMapRotation;y&&(p.envMap.value=y,p.envMapRotation.value.setFromMatrix4(Vp.makeRotationFromEuler(E)).transpose(),y.isCubeTexture&&y.isRenderTargetTexture===!1&&p.envMapRotation.value.premultiply(_l),p.reflectivity.value=u.reflectivity,p.ior.value=u.ior,p.refractionRatio.value=u.refractionRatio),u.lightMap&&(p.lightMap.value=u.lightMap,p.lightMapIntensity.value=u.lightMapIntensity,t(u.lightMap,p.lightMapTransform)),u.aoMap&&(p.aoMap.value=u.aoMap,p.aoMapIntensity.value=u.aoMapIntensity,t(u.aoMap,p.aoMapTransform))}function a(p,u){p.diffuse.value.copy(u.color),p.opacity.value=u.opacity,u.map&&(p.map.value=u.map,t(u.map,p.mapTransform))}function o(p,u){p.dashSize.value=u.dashSize,p.totalSize.value=u.dashSize+u.gapSize,p.scale.value=u.scale}function d(p,u,b,y){p.diffuse.value.copy(u.color),p.opacity.value=u.opacity,p.size.value=u.size*b,p.scale.value=y*.5,u.map&&(p.map.value=u.map,t(u.map,p.uvTransform)),u.alphaMap&&(p.alphaMap.value=u.alphaMap,t(u.alphaMap,p.alphaMapTransform)),u.alphaTest>0&&(p.alphaTest.value=u.alphaTest)}function c(p,u){p.diffuse.value.copy(u.color),p.opacity.value=u.opacity,p.rotation.value=u.rotation,u.map&&(p.map.value=u.map,t(u.map,p.mapTransform)),u.alphaMap&&(p.alphaMap.value=u.alphaMap,t(u.alphaMap,p.alphaMapTransform)),u.alphaTest>0&&(p.alphaTest.value=u.alphaTest)}function m(p,u){p.specular.value.copy(u.specular),p.shininess.value=Math.max(u.shininess,1e-4)}function x(p,u){u.gradientMap&&(p.gradientMap.value=u.gradientMap)}function f(p,u){p.metalness.value=u.metalness,u.metalnessMap&&(p.metalnessMap.value=u.metalnessMap,t(u.metalnessMap,p.metalnessMapTransform)),p.roughness.value=u.roughness,u.roughnessMap&&(p.roughnessMap.value=u.roughnessMap,t(u.roughnessMap,p.roughnessMapTransform)),u.envMap&&(p.envMapIntensity.value=u.envMapIntensity)}function g(p,u,b){p.ior.value=u.ior,u.diffuseRoughness>0&&(p.diffuseRoughness.value=u.diffuseRoughness,u.diffuseRoughnessMap&&(p.diffuseRoughnessMap.value=u.diffuseRoughnessMap,t(u.diffuseRoughnessMap,p.diffuseRoughnessMapTransform))),u.sheen>0&&(p.sheenColor.value.copy(u.sheenColor).multiplyScalar(u.sheen),p.sheenRoughness.value=u.sheenRoughness,u.sheenColorMap&&(p.sheenColorMap.value=u.sheenColorMap,t(u.sheenColorMap,p.sheenColorMapTransform)),u.sheenRoughnessMap&&(p.sheenRoughnessMap.value=u.sheenRoughnessMap,t(u.sheenRoughnessMap,p.sheenRoughnessMapTransform))),u.clearcoat>0&&(p.clearcoat.value=u.clearcoat,p.clearcoatRoughness.value=u.clearcoatRoughness,u.clearcoatMap&&(p.clearcoatMap.value=u.clearcoatMap,t(u.clearcoatMap,p.clearcoatMapTransform)),u.clearcoatRoughnessMap&&(p.clearcoatRoughnessMap.value=u.clearcoatRoughnessMap,t(u.clearcoatRoughnessMap,p.clearcoatRoughnessMapTransform)),u.clearcoatNormalMap&&(p.clearcoatNormalMap.value=u.clearcoatNormalMap,t(u.clearcoatNormalMap,p.clearcoatNormalMapTransform),p.clearcoatNormalScale.value.copy(u.clearcoatNormalScale),u.side===Xt&&p.clearcoatNormalScale.value.negate())),u.dispersion>0&&(p.dispersion.value=u.dispersion),u.retroreflectivity>0&&(p.retroreflectivity.value=u.retroreflectivity),u.iridescence>0&&(p.iridescence.value=u.iridescence,p.iridescenceIOR.value=u.iridescenceIOR,p.iridescenceThicknessMinimum.value=u.iridescenceThicknessMinimum,p.iridescenceThicknessMaximum.value=u.iridescenceThicknessMaximum,u.iridescenceMap&&(p.iridescenceMap.value=u.iridescenceMap,t(u.iridescenceMap,p.iridescenceMapTransform)),u.iridescenceThicknessMap&&(p.iridescenceThicknessMap.value=u.iridescenceThicknessMap,t(u.iridescenceThicknessMap,p.iridescenceThicknessMapTransform))),u.transmission>0&&(p.transmission.value=u.transmission,p.transmissionSamplerMap.value=b.texture,p.transmissionSamplerSize.value.set(b.width,b.height),u.transmissionMap&&(p.transmissionMap.value=u.transmissionMap,t(u.transmissionMap,p.transmissionMapTransform)),p.thickness.value=u.thickness,u.thicknessMap&&(p.thicknessMap.value=u.thicknessMap,t(u.thicknessMap,p.thicknessMapTransform)),p.attenuationDistance.value=u.attenuationDistance,p.attenuationColor.value.copy(u.attenuationColor)),u.anisotropy>0&&(p.anisotropyVector.value.set(u.anisotropy*Math.cos(u.anisotropyRotation),u.anisotropy*Math.sin(u.anisotropyRotation)),u.anisotropyMap&&(p.anisotropyMap.value=u.anisotropyMap,t(u.anisotropyMap,p.anisotropyMapTransform))),p.specularIntensity.value=u.specularIntensity,p.specularColor.value.copy(u.specularColor),u.specularColorMap&&(p.specularColorMap.value=u.specularColorMap,t(u.specularColorMap,p.specularColorMapTransform)),u.specularIntensityMap&&(p.specularIntensityMap.value=u.specularIntensityMap,t(u.specularIntensityMap,p.specularIntensityMapTransform))}function l(p,u){u.matcap&&(p.matcap.value=u.matcap)}function T(p,u){const b=e.get(u).light;p.referencePosition.value.setFromMatrixPosition(b.matrixWorld),p.nearDistance.value=b.shadow.camera.near,p.farDistance.value=b.shadow.camera.far}return{refreshFogUniforms:n,refreshMaterialUniforms:s}}function Xp(i,e,t,n){let s={},r={};function a(b,y){const E=y.program;n.uniformBlockBinding(b,E,s[b.id])}function o(b,y){let E=s[b.id];E===void 0&&(l(b),E=d(b),s[b.id]=E,b.addEventListener("dispose",p));const R=y.program;n.updateUBOMapping(b,R);const A=e.render.frame;r[b.id]!==A&&(c(b),r[b.id]=A)}function d(b){const y=i.createBuffer(),E=b.__size,R=b.usage;return i.bindBuffer(i.UNIFORM_BUFFER,y),i.bufferData(i.UNIFORM_BUFFER,E,R),i.bindBuffer(i.UNIFORM_BUFFER,null),y}function c(b){const y=s[b.id],E=b.uniforms,R=b.__cache;i.bindBuffer(i.UNIFORM_BUFFER,y);for(let A=0,h=E.length;A<h;A++){const M=E[A];if(Array.isArray(M))for(let I=0,C=M.length;I<C;I++)m(M[I],A,I,R);else m(M,A,0,R)}i.bindBuffer(i.UNIFORM_BUFFER,null)}function m(b,y,E,R){if(f(b,y,E,R)===!0){const A=b.__offset,h=b.value;if(Array.isArray(h)){let M=0;for(let I=0;I<h.length;I++){const C=h[I],U=T(C,!0);x(C,b.__data,M),M+=U.storage/Float32Array.BYTES_PER_ELEMENT}}else x(h,b.__data,0);i.bufferSubData(i.UNIFORM_BUFFER,A,b.__data)}}function x(b,y,E){typeof b=="number"||typeof b=="boolean"?y[E]=b:b.isMatrix3?(y[E]=b.elements[0],y[E+1]=b.elements[1],y[E+2]=b.elements[2],y[E+3]=0,y[E+4]=b.elements[3],y[E+5]=b.elements[4],y[E+6]=b.elements[5],y[E+7]=0,y[E+8]=b.elements[6],y[E+9]=b.elements[7],y[E+10]=b.elements[8],y[E+11]=0):ArrayBuffer.isView(b)?y.set(b.subarray(0,y.length-E),E):b.toArray(y,E)}function f(b,y,E,R){const A=b.value,h=y+"_"+E;if(Array.isArray(A)){R[h]===void 0&&(R[h]=[]);const M=R[h];let I=!1;for(let C=0;C<A.length;C++)g(A[C],M,C)===!0&&(I=!0);return I}return g(A,R,h)}function g(b,y,E){if(y[E]===void 0)return typeof b=="number"||typeof b=="boolean"?y[E]=b:ArrayBuffer.isView(b)?y[E]=b.slice():y[E]=b.clone(),!0;{const R=y[E];if(typeof b=="number"||typeof b=="boolean"){if(R!==b)return y[E]=b,!0}else{if(ArrayBuffer.isView(b))return!0;if(R.equals(b)===!1)return R.copy(b),!0}}return!1}function l(b){const y=b.uniforms;let E=0;const R=16;for(let h=0,M=y.length;h<M;h++){const I=Array.isArray(y[h])?y[h]:[y[h]];for(let C=0,U=I.length;C<U;C++){const F=I[C],X=Array.isArray(F.value)?F.value:[F.value],L=Array.isArray(y[h])||Array.isArray(F.value);let W=E;for(let H=0,ne=X.length;H<ne;H++){const q=X[H],ee=T(q,L),te=E%R,Me=te%ee.boundary,Le=te+Me;E+=Me,Le!==0&&R-Le<ee.storage&&(E+=R-Le),H===0&&(W=E),E+=ee.storage}const K=F.type==="int"?Int32Array:F.type==="uint"?Uint32Array:Float32Array;F.__data=new K((E-W)/Float32Array.BYTES_PER_ELEMENT),F.__offset=W}}const A=E%R;return A>0&&(E+=R-A),b.__size=E,b.__cache={},this}function T(b,y){const E={boundary:0,storage:0};return typeof b=="number"||typeof b=="boolean"?(E.boundary=4,E.storage=4):b.isVector2?(E.boundary=8,E.storage=8):b.isVector3||b.isColor?(E.boundary=16,E.storage=12):b.isVector4?(E.boundary=16,E.storage=16):b.isMatrix3?(E.boundary=16,E.storage=48):b.isMatrix4?(E.boundary=16,E.storage=64):b.isTexture?De("WebGLRenderer: Texture samplers can not be part of an uniforms group."):ArrayBuffer.isView(b)?(E.boundary=16,E.storage=b.byteLength):De("WebGLRenderer: Unsupported uniform value type.",b),y===!0&&(E.boundary=16,E.storage=Math.ceil(E.storage/16)*16),E}function p(b){const y=b.target;y.removeEventListener("dispose",p),i.deleteBuffer(s[y.id]),delete s[y.id],delete r[y.id]}function u(){for(const b in s)i.deleteBuffer(s[b]);s={},r={}}return{bind:a,update:o,dispose:u}}const qp=new Uint16Array([12469,15057,12620,14925,13266,14620,13807,14376,14323,13990,14545,13625,14713,13328,14840,12882,14931,12528,14996,12233,15039,11829,15066,11525,15080,11295,15085,10976,15082,10705,15073,10495,13880,14564,13898,14542,13977,14430,14158,14124,14393,13732,14556,13410,14702,12996,14814,12596,14891,12291,14937,11834,14957,11489,14958,11194,14943,10803,14921,10506,14893,10278,14858,9960,14484,14039,14487,14025,14499,13941,14524,13740,14574,13468,14654,13106,14743,12678,14818,12344,14867,11893,14889,11509,14893,11180,14881,10751,14852,10428,14812,10128,14765,9754,14712,9466,14764,13480,14764,13475,14766,13440,14766,13347,14769,13070,14786,12713,14816,12387,14844,11957,14860,11549,14868,11215,14855,10751,14825,10403,14782,10044,14729,9651,14666,9352,14599,9029,14967,12835,14966,12831,14963,12804,14954,12723,14936,12564,14917,12347,14900,11958,14886,11569,14878,11247,14859,10765,14828,10401,14784,10011,14727,9600,14660,9289,14586,8893,14508,8533,15111,12234,15110,12234,15104,12216,15092,12156,15067,12010,15028,11776,14981,11500,14942,11205,14902,10752,14861,10393,14812,9991,14752,9570,14682,9252,14603,8808,14519,8445,14431,8145,15209,11449,15208,11451,15202,11451,15190,11438,15163,11384,15117,11274,15055,10979,14994,10648,14932,10343,14871,9936,14803,9532,14729,9218,14645,8742,14556,8381,14461,8020,14365,7603,15273,10603,15272,10607,15267,10619,15256,10631,15231,10614,15182,10535,15118,10389,15042,10167,14963,9787,14883,9447,14800,9115,14710,8665,14615,8318,14514,7911,14411,7507,14279,7198,15314,9675,15313,9683,15309,9712,15298,9759,15277,9797,15229,9773,15166,9668,15084,9487,14995,9274,14898,8910,14800,8539,14697,8234,14590,7790,14479,7409,14367,7067,14178,6621,15337,8619,15337,8631,15333,8677,15325,8769,15305,8871,15264,8940,15202,8909,15119,8775,15022,8565,14916,8328,14804,8009,14688,7614,14569,7287,14448,6888,14321,6483,14088,6171,15350,7402,15350,7419,15347,7480,15340,7613,15322,7804,15287,7973,15229,8057,15148,8012,15046,7846,14933,7611,14810,7357,14682,7069,14552,6656,14421,6316,14251,5948,14007,5528,15356,5942,15356,5977,15353,6119,15348,6294,15332,6551,15302,6824,15249,7044,15171,7122,15070,7050,14949,6861,14818,6611,14679,6349,14538,6067,14398,5651,14189,5311,13935,4958,15359,4123,15359,4153,15356,4296,15353,4646,15338,5160,15311,5508,15263,5829,15188,6042,15088,6094,14966,6001,14826,5796,14678,5543,14527,5287,14377,4985,14133,4586,13869,4257,15360,1563,15360,1642,15358,2076,15354,2636,15341,3350,15317,4019,15273,4429,15203,4732,15105,4911,14981,4932,14836,4818,14679,4621,14517,4386,14359,4156,14083,3795,13808,3437,15360,122,15360,137,15358,285,15355,636,15344,1274,15322,2177,15281,2765,15215,3223,15120,3451,14995,3569,14846,3567,14681,3466,14511,3305,14344,3121,14037,2800,13753,2467,15360,0,15360,1,15359,21,15355,89,15346,253,15325,479,15287,796,15225,1148,15133,1492,15008,1749,14856,1882,14685,1886,14506,1783,14324,1608,13996,1398,13702,1183]);let un=null;function Yp(){return un===null&&(un=new kc(qp,16,16,Qn,xn),un.name="DFG_LUT",un.minFilter=Wt,un.magFilter=Wt,un.wrapS=Tn,un.wrapT=Tn,un.generateMipmaps=!1,un.needsUpdate=!0),un}class Kp{constructor(e={}){const{canvas:t=cc(),context:n=null,depth:s=!0,stencil:r=!1,alpha:a=!1,antialias:o=!1,premultipliedAlpha:d=!0,preserveDrawingBuffer:c=!1,powerPreference:m="default",failIfMajorPerformanceCaveat:x=!1,reversedDepthBuffer:f=!1,outputBufferType:g=Zt}=e;this.isWebGLRenderer=!0;let l;if(n!==null){if(typeof WebGLRenderingContext<"u"&&n instanceof WebGLRenderingContext)throw new Error("THREE.WebGLRenderer: WebGL 1 is not supported since r163.");l=n.getContextAttributes().alpha}else l=a;const T=g,p=new Set([ra,sa,ia]),u=new Set([Zt,gn,Ni,_i,ta,na]),b=new Uint32Array(4),y=new Int32Array(4),E={textures:[],min:[],max:[],resolution:[]};let R=null,A=null;const h=[],M=[];let I=null;this.domElement=t,this.debug={checkShaderErrors:!0,diagnostics:{keywords:!1},onShaderError:null},this.autoClear=!0,this.autoClearColor=!0,this.autoClearDepth=!0,this.autoClearStencil=!0,this.sortObjects=!0,this.clippingPlanes=[],this.localClippingEnabled=!1,this.toneMapping=wn,this.toneMappingExposure=1,this.transmissionResolutionScale=1;const C=this;let U=!1,F=null,X=null,L=null,W=null;this._outputColorSpace=Jt;let K=0,H=0,ne=null,q=-1,ee=null;const te=new pt,Me=new pt;let Le=null;const $=new je(0);let pe=0,ve=t.width,Ae=t.height,me=1,Ie=null,gt=null;const Re=new pt(0,0,ve,Ae),et=new pt(0,0,ve,Ae);let it=!1;const Ge=new Fi,bt=new ca;let xt=!1,Pt=!1;const ct=new mt,qt=new z,vt=new pt,Ft={background:null,fog:null,environment:null,overrideMaterial:null,isScene:!0};let Ot=!1;function Gt(){return ne===null?me:1}let N=n;function Ht(S,D){return t.getContext(S,D)}let ot,dt,le,st,Ce,Pe,w,v,B,ie,j,ae,fe,Z,J,ce,P,Q,Y,de,re,oe,Te;try{const S={alpha:!0,depth:s,stencil:r,antialias:o,premultipliedAlpha:d,preserveDrawingBuffer:c,powerPreference:m,failIfMajorPerformanceCaveat:x};if("setAttribute"in t&&t.setAttribute("data-engine",`three.js r${Qr}`),t.addEventListener("webglcontextlost",we,!1),t.addEventListener("webglcontextrestored",ze,!1),t.addEventListener("webglcontextcreationerror",ge,!1),N===null){const D="webgl2";if(N=Ht(D,S),N===null)throw Ht(D)?new Error("THREE.WebGLRenderer: Error creating WebGL context with your selected attributes."):new Error("THREE.WebGLRenderer: Error creating WebGL context.")}He()}catch(S){throw t.removeEventListener("webglcontextlost",we,!1),t.removeEventListener("webglcontextrestored",ze,!1),t.removeEventListener("webglcontextcreationerror",ge,!1),nt("WebGLRenderer: "+S.message),S}function He(){ot=new Jf(N),ot.init(),re=new Bp(N,ot),dt=new zf(N,ot,e,re),le=new Fp(N,ot),dt.reversedDepthBuffer&&f&&le.buffers.depth.setReversed(!0),X=N.createFramebuffer(),L=N.createFramebuffer(),W=N.createFramebuffer(),st=new eh(N),Ce=new bp,Pe=new Op(N,ot,le,Ce,dt,re,st),w=new $f(C),v=new td(N),oe=new Bf(N,v),B=new Qf(N,v,st,oe),ie=new nh(N,B,v,oe,st),Q=new th(N,dt,Pe),J=new Gf(Ce),j=new Mp(C,w,ot,dt,oe,J),ae=new Wp(C,Ce),fe=new Ep,Z=new Pp(ot),P=new Of(C,w,le,ie,l,d),ce=new Up(C,ie,dt),Te=new Xp(N,st,dt,le),Y=new kf(N,ot,st),de=new jf(N,ot,st),st.programs=j.programs,C.capabilities=dt,C.extensions=ot,C.properties=Ce,C.renderLists=fe,C.shadowMap=ce,C.state=le,C.info=st}T!==Zt&&(I=new sh(T,t.width,t.height,o,s,r));const se=new Hp(C,N);this.xr=se,this.getContext=function(){return N},this.getContextAttributes=function(){return N.getContextAttributes()},this.forceContextLoss=function(){const S=ot.get("WEBGL_lose_context");S&&S.loseContext()},this.forceContextRestore=function(){const S=ot.get("WEBGL_lose_context");S&&S.restoreContext()},this.getPixelRatio=function(){return me},this.setPixelRatio=function(S){S!==void 0&&(me=S,this.setSize(ve,Ae,!1))},this.getSize=function(S){return S.set(ve,Ae)},this.setSize=function(S,D,G=!0){if(se.isPresenting){De("WebGLRenderer: Can't change size while VR device is presenting.");return}ve=S,Ae=D,t.width=Math.floor(S*me),t.height=Math.floor(D*me),G===!0&&(t.style.width=S+"px",t.style.height=D+"px"),I!==null&&I.setSize(t.width,t.height),this.setViewport(0,0,S,D)},this.getDrawingBufferSize=function(S){return S.set(ve*me,Ae*me).floor()},this.setDrawingBufferSize=function(S,D,G){ve=S,Ae=D,me=G,t.width=Math.floor(S*G),t.height=Math.floor(D*G),this.setViewport(0,0,S,D)},this.setEffects=function(S){if(T===Zt){nt("WebGLRenderer: setEffects() requires outputBufferType set to HalfFloatType or FloatType.");return}if(S){for(let D=0;D<S.length;D++)if(S[D].isOutputPass===!0){De("WebGLRenderer: OutputPass is not needed in setEffects(). Tone mapping and color space conversion are applied automatically.");break}}I.setEffects(S||[])},this.getCurrentViewport=function(S){return S.copy(te)},this.getViewport=function(S){return S.copy(Re)},this.setViewport=function(S,D,G,O){S.isVector4?Re.set(S.x,S.y,S.z,S.w):Re.set(S,D,G,O),le.viewport(te.copy(Re).multiplyScalar(Gt()).round())},this.getScissor=function(S){return S.copy(et)},this.setScissor=function(S,D,G,O){S.isVector4?et.set(S.x,S.y,S.z,S.w):et.set(S,D,G,O),le.scissor(Me.copy(et).multiplyScalar(Gt()).round())},this.getScissorTest=function(){return it},this.setScissorTest=function(S){le.setScissorTest(it=S)},this.setOpaqueSort=function(S){Ie=S},this.setTransparentSort=function(S){gt=S},this.getClearColor=function(S){return S.copy(P.getClearColor())},this.setClearColor=function(){P.setClearColor(...arguments)},this.getClearAlpha=function(){return P.getClearAlpha()},this.setClearAlpha=function(){P.setClearAlpha(...arguments)},this.clear=function(S=!0,D=!0,G=!0){let O=0;if(S){let k=!1;if(ne!==null){const he=ne.texture.format;k=p.has(he)}if(k){const he=ne.texture.type,Se=u.has(he),ue=P.getClearColor(),_e=P.getClearAlpha(),Ee=ue.r,Ue=ue.g,Ye=ue.b;Se?(b[0]=Ee,b[1]=Ue,b[2]=Ye,b[3]=_e,N.clearBufferuiv(N.COLOR,0,b)):(y[0]=Ee,y[1]=Ue,y[2]=Ye,y[3]=_e,N.clearBufferiv(N.COLOR,0,y))}else O|=N.COLOR_BUFFER_BIT}D&&(O|=N.DEPTH_BUFFER_BIT,this.state.buffers.depth.setMask(!0)),G&&(O|=N.STENCIL_BUFFER_BIT,this.state.buffers.stencil.setMask(4294967295)),O!==0&&N.clear(O)},this.clearColor=function(){this.clear(!0,!1,!1)},this.clearDepth=function(){this.clear(!1,!0,!1)},this.clearStencil=function(){this.clear(!1,!1,!0)},this.setNodesHandler=function(S){S.setRenderer(this),F=S},this.dispose=function(){t.removeEventListener("webglcontextlost",we,!1),t.removeEventListener("webglcontextrestored",ze,!1),t.removeEventListener("webglcontextcreationerror",ge,!1),P.dispose(),fe.dispose(),Z.dispose(),Pe.dispose(),Ce.dispose(),w.dispose(),ie.dispose(),B.dispose(),oe.dispose(),Te.dispose(),j.dispose(),se.dispose(),se.removeEventListener("sessionstart",va),se.removeEventListener("sessionend",Sa),Hn.stop()};function we(S){S.preventDefault(),Fa("WebGLRenderer: Context Lost."),U=!0}function ze(){Fa("WebGLRenderer: Context Restored."),U=!1;const S=st.autoReset,D=ce.enabled,G=ce.autoUpdate,O=ce.needsUpdate,k=ce.type;He(),st.autoReset=S,ce.enabled=D,ce.autoUpdate=G,ce.needsUpdate=O,ce.type=k}function ge(S){nt("WebGLRenderer: A WebGL context could not be created. Reason: ",S.statusMessage)}function rt(S){const D=S.target;D.removeEventListener("dispose",rt),Tt(D)}function Tt(S){Rn(S),Ce.remove(S)}function Rn(S){const D=Ce.get(S).programs;D!==void 0&&(D.forEach(function(G){j.releaseProgram(G)}),S.isShaderMaterial&&j.releaseShaderCache(S))}this.renderBufferDirect=function(S,D,G,O,k,he){D===null&&(D=Ft);const Se=k.isMesh&&k.matrixWorld.determinantAffine()<0,ue=Ml(S,D,G,O,k);le.setMaterial(O,Se);let _e=G.index,Ee=1;if(O.wireframe===!0){if(_e=B.getWireframeAttribute(G),_e===void 0)return;Ee=2}const Ue=G.drawRange,Ye=G.attributes.position;let be=Ue.start*Ee,Ke=(Ue.start+Ue.count)*Ee;he!==null&&(be=Math.max(be,he.start*Ee),Ke=Math.min(Ke,(he.start+he.count)*Ee)),_e!==null?(be=Math.max(be,0),Ke=Math.min(Ke,_e.count)):Ye!=null&&(be=Math.max(be,0),Ke=Math.min(Ke,Ye.count));const St=Ke-be;if(St<0||St===1/0)return;oe.setup(k,O,ue,G,_e);let ut,lt=Y;if(_e!==null&&(ut=v.get(_e),lt=de,lt.setIndex(ut)),k.isMesh)O.wireframe===!0?(le.setLineWidth(O.wireframeLinewidth*Gt()),lt.setMode(N.LINES)):lt.setMode(N.TRIANGLES);else if(k.isLine){let Lt=O.linewidth;Lt===void 0&&(Lt=1),le.setLineWidth(Lt*Gt()),k.isLineSegments?lt.setMode(N.LINES):k.isLineLoop?lt.setMode(N.LINE_LOOP):lt.setMode(N.LINE_STRIP)}else k.isPoints?lt.setMode(N.POINTS):k.isSprite&&lt.setMode(N.TRIANGLES);if(k.isBatchedMesh)if(ot.get("WEBGL_multi_draw"))lt.renderMultiDraw(k._multiDrawStarts,k._multiDrawCounts,k._multiDrawCount);else{const Lt=k._multiDrawStarts,ye=k._multiDrawCounts,Bt=k._multiDrawCount,tt=_e?v.get(_e).bytesPerElement:1,Vn=Ce.get(O).currentProgram.getUniforms();for(let Dt=0;Dt<Bt;Dt++)Vn.setValue(N,"_gl_DrawID",Dt),lt.render(Lt[Dt]/tt,ye[Dt])}else if(k.isInstancedMesh)lt.renderInstances(be,St,k.count);else if(G.isInstancedBufferGeometry){const Lt=G._maxInstanceCount!==void 0?G._maxInstanceCount:1/0,ye=Math.min(G.instanceCount,Lt);lt.renderInstances(be,St,ye)}else lt.render(be,St)};function _a(S,D,G,O){F!==null&&S.isNodeMaterial&&F.setObject(O,S),xt===!0&&J.setState(S,G,!1),S.transparent===!0&&S.side===En&&S.forceSinglePass===!1?(S.side=Xt,S.needsUpdate=!0,yi(S,D,O),S.side=Jn,S.needsUpdate=!0,yi(S,D,O),S.side=En):yi(S,D,O)}this.compile=function(S,D,G=null){G===null&&(G=S),F!==null&&F.renderStart(S,D,G),A=Z.get(G),A.init(D),M.push(A),G.traverseVisible(function(k){k.isLight&&k.layers.test(D.layers)&&(A.pushLight(k),k.castShadow&&A.pushShadow(k))}),S!==G&&S.traverseVisible(function(k){k.isLight&&k.layers.test(D.layers)&&(A.pushLight(k),k.castShadow&&A.pushShadow(k))}),A.setupLights(),F!==null&&F.updateLights(A.state.lightsArray),Pt=this.localClippingEnabled,xt=J.init(this.clippingPlanes,Pt),xt===!0&&J.setGlobalState(this.clippingPlanes,D),F!==null&&ce.render(A.state.shadowsArray,G,D);const O=new Set;return S.traverse(function(k){if(!(k.isMesh||k.isPoints||k.isLine||k.isSprite))return;const he=k.material;if(he)if(Array.isArray(he))for(let Se=0;Se<he.length;Se++){const ue=he[Se];_a(ue,G,D,k),O.add(ue)}else _a(he,G,D,k),O.add(he)}),A=M.pop(),F!==null&&F.renderEnd(),O},this.compileAsync=function(S,D,G=null){const O=this.compile(S,D,G);return new Promise(k=>{function he(){if(O.forEach(function(Se){const _e=Ce.get(Se).currentProgram;(_e===void 0||_e.isReady())&&O.delete(Se)}),O.size===0){k(S);return}setTimeout(he,10)}ot.get("KHR_parallel_shader_compile")!==null?he():setTimeout(he,10)})};let Ds=null;function vl(S){Ds&&Ds(S)}function va(){Hn.stop()}function Sa(){Hn.start()}const Hn=new ll;Hn.setAnimationLoop(vl),typeof self<"u"&&Hn.setContext(self),this.setAnimationLoop=function(S){Ds=S,se.setAnimationLoop(S),S===null?Hn.stop():Hn.start()},se.addEventListener("sessionstart",va),se.addEventListener("sessionend",Sa),this.render=function(S,D){if(D!==void 0&&D.isCamera!==!0){nt("WebGLRenderer.render: camera is not an instance of THREE.Camera.");return}if(U===!0)return;F!==null&&F.renderStart(S,D);const G=se.enabled===!0&&se.isPresenting===!0,O=I!==null&&(ne===null||G)&&I.begin(C,ne);if(S.matrixWorldAutoUpdate===!0&&S.updateMatrixWorld(),D.parent===null&&D.matrixWorldAutoUpdate===!0&&D.updateMatrixWorld(),se.enabled===!0&&se.isPresenting===!0&&(I===null||I.isCompositing()===!1)&&(se.cameraAutoUpdate===!0&&se.updateCamera(D),D=se.getCamera()),S.isScene===!0&&S.onBeforeRender(C,S,D,ne),A=Z.get(S,M.length),A.init(D),A.state.textureUnits=Pe.getTextureUnits(),M.push(A),ct.multiplyMatrices(D.projectionMatrix,D.matrixWorldInverse),D.isArrayCamera?bt.setFromArrayCamera(D):Ge.setFromProjectionMatrix(ct,ln,D.reversedDepth),Pt=this.localClippingEnabled,xt=J.init(this.clippingPlanes,Pt),R=fe.get(S,h.length),R.init(),h.push(R),this.info.render.frame++,se.enabled===!0&&se.isPresenting===!0){const Se=C.xr.getDepthSensingMesh();Se!==null&&Is(Se,D,-1/0,C.sortObjects)}Is(S,D,0,C.sortObjects),R.finish(),F!==null&&F.updateLights(A.state.lightsArray),C.sortObjects===!0&&R.sort(Ie,gt),Ot=se.enabled===!1||se.isPresenting===!1||se.hasDepthSensing()===!1,Ot&&P.addToRenderList(R,S),this.info.autoReset===!0&&this.info.reset(),xt===!0&&J.beginShadows();const k=A.state.shadowsArray;if(ce.render(k,S,D),xt===!0&&J.endShadows(),(O&&I.hasRenderPass())===!1){const Se=R.opaque,ue=R.transmissive;if(A.setupLights(),D.isArrayCamera){const _e=D.cameras;if(ue.length>0)for(let Ee=0,Ue=_e.length;Ee<Ue;Ee++){const Ye=_e[Ee];ba(Se,ue,S,Ye)}Ot&&P.render(S);for(let Ee=0,Ue=_e.length;Ee<Ue;Ee++){const Ye=_e[Ee];Ma(R,S,Ye,Ye.viewport)}}else ue.length>0&&ba(Se,ue,S,D),Ot&&P.render(S),Ma(R,S,D)}ne!==null&&H===0&&(Pe.updateMultisampleRenderTarget(ne),Pe.updateRenderTargetMipmap(ne)),O&&I.end(C),S.isScene===!0&&S.onAfterRender(C,S,D),oe.resetDefaultState(),q=-1,ee=null,M.pop(),M.length>0?(A=M[M.length-1],Pe.setTextureUnits(A.state.textureUnits),xt===!0&&J.setGlobalState(C.clippingPlanes,A.state.camera)):A=null,h.pop(),h.length>0?R=h[h.length-1]:R=null,F!==null&&F.renderEnd()};function Is(S,D,G,O){if(S.visible===!1)return;if(S.layers.test(D.layers)){if(S.isGroup)G=S.renderOrder;else if(S.isLOD)S.autoUpdate===!0&&S.update(D);else if(S.isLight)A.pushLight(S),S.castShadow&&A.pushShadow(S);else if(S.isSprite){const Se=D.isArrayCamera?bt:Ge;if(!S.frustumCulled||S.intersectsFrustum(Se)){O&&vt.setFromMatrixPosition(S.matrixWorld).applyMatrix4(ct);const ue=ie.update(S),_e=S.material;_e.visible&&R.push(S,ue,_e,G,vt.z,null,D)}}else if(S.isMesh||S.isLine||S.isPoints){const Se=D.isArrayCamera?bt:Ge;if(!S.frustumCulled||S.intersectsFrustum(Se)){const ue=ie.update(S),_e=S.material;if(O&&(S.boundingSphere!==void 0?(S.boundingSphere===null&&S.computeBoundingSphere(),vt.copy(S.boundingSphere.center)):(ue.boundingSphere===null&&ue.computeBoundingSphere(),vt.copy(ue.boundingSphere.center)),vt.applyMatrix4(S.matrixWorld).applyMatrix4(ct)),Array.isArray(_e)){const Ee=ue.groups;for(let Ue=0,Ye=Ee.length;Ue<Ye;Ue++){const be=Ee[Ue],Ke=_e[be.materialIndex];Ke&&Ke.visible&&R.push(S,ue,Ke,G,vt.z,be,D)}}else _e.visible&&R.push(S,ue,_e,G,vt.z,null,D)}}}const he=S.children;for(let Se=0,ue=he.length;Se<ue;Se++)Is(he[Se],D,G,O)}function Ma(S,D,G,O){const{opaque:k,transmissive:he,transparent:Se}=S;A.setupLightsView(G),xt===!0&&J.setGlobalState(C.clippingPlanes,G),O&&le.viewport(te.copy(O)),k.length>0&&Gi(k,D,G),he.length>0&&Gi(he,D,G),Se.length>0&&Gi(Se,D,G),le.buffers.depth.setTest(!0),le.buffers.depth.setMask(!0),le.buffers.color.setMask(!0),le.setPolygonOffset(!1)}function ba(S,D,G,O){if((G.isScene===!0?G.overrideMaterial:null)!==null)return;if(A.state.transmissionRenderTarget[O.id]===void 0){const be=ot.has("EXT_color_buffer_half_float")||ot.has("EXT_color_buffer_float");A.state.transmissionRenderTarget[O.id]=new mn(1,1,{generateMipmaps:!0,type:be?xn:Zt,minFilter:On,samples:Math.max(4,dt.samples),stencilBuffer:r,resolveDepthBuffer:!1,resolveStencilBuffer:!1,storeMultisampledDepthBuffer:!1,storeMultisampledStencilBuffer:!1,colorSpace:Ze.workingColorSpace})}const he=A.state.transmissionRenderTarget[O.id],Se=O.viewport||te;he.setSize(Se.z*C.transmissionResolutionScale,Se.w*C.transmissionResolutionScale);const ue=C.getRenderTarget(),_e=C.getActiveCubeFace(),Ee=C.getActiveMipmapLevel();C.setRenderTarget(he),C.getClearColor($),pe=C.getClearAlpha(),pe<1&&C.setClearColor(16777215,.5),C.clear(),Ot&&P.render(G);const Ue=C.toneMapping;C.toneMapping=wn;const Ye=O.viewport;if(O.viewport!==void 0&&(O.viewport=void 0),A.setupLightsView(O),xt===!0&&J.setGlobalState(C.clippingPlanes,O),Gi(S,G,O),Pe.updateMultisampleRenderTarget(he),Pe.updateRenderTargetMipmap(he),ot.has("WEBGL_multisampled_render_to_texture")===!1){let be=!1;for(let Ke=0,St=D.length;Ke<St;Ke++){const ut=D[Ke],{object:lt,geometry:Lt,material:ye,group:Bt}=ut;if(ye.side===En&&lt.layers.test(O.layers)){const tt=ye.side;ye.side=Xt,ye.needsUpdate=!0,ya(lt,G,O,Lt,ye,Bt),ye.side=tt,ye.needsUpdate=!0,be=!0}}be===!0&&(Pe.updateMultisampleRenderTarget(he),Pe.updateRenderTargetMipmap(he))}C.setRenderTarget(ue,_e,Ee),C.setClearColor($,pe),Ye!==void 0&&(O.viewport=Ye),C.toneMapping=Ue}function Gi(S,D,G){const O=D.isScene===!0?D.overrideMaterial:null;for(let k=0,he=S.length;k<he;k++){const Se=S[k],{object:ue,geometry:_e,group:Ee}=Se;let Ue=Se.material;Ue.allowOverride===!0&&O!==null&&(Ue=O),ue.layers.test(G.layers)&&ya(ue,D,G,_e,Ue,Ee)}}function ya(S,D,G,O,k,he){F!==null&&k.isNodeMaterial&&F.setObject(S,k),S.onBeforeRender(C,D,G,O,k,he),S.modelViewMatrix.multiplyMatrices(G.matrixWorldInverse,S.matrixWorld),S.normalMatrix.getNormalMatrix(S.modelViewMatrix),k.onBeforeRender(C,D,G,O,S,he),k.transparent===!0&&k.side===En&&k.forceSinglePass===!1?(k.side=Xt,k.needsUpdate=!0,C.renderBufferDirect(G,D,O,k,S,he),k.side=Jn,k.needsUpdate=!0,C.renderBufferDirect(G,D,O,k,S,he),k.side=En):C.renderBufferDirect(G,D,O,k,S,he),S.onAfterRender(C,D,G,O,k,he)}function Ea(S){let D=0;for(const G of S)G.texture!==null&&D++;return D}function yi(S,D,G){D.isScene!==!0&&(D=Ft);const O=Ce.get(S),k=A.state.lights,he=A.state.shadowsArray,Se=k.state.version,ue=j.getParameters(S,k.state,he,D,G,A.state.lightProbeGridArray),_e=j.getProgramCacheKey(ue);let Ee=O.programs;O.environment=S.isMeshStandardMaterial||S.isMeshLambertMaterial||S.isMeshPhongMaterial?D.environment:null,O.fog=D.fog;const Ue=S.isMeshStandardMaterial||S.isMeshLambertMaterial&&!S.envMap||S.isMeshPhongMaterial&&!S.envMap;O.envMap=w.get(S.envMap||O.environment,Ue),O.envMapRotation=O.environment!==null&&S.envMap===null?D.environmentRotation:S.envMapRotation,Ee===void 0&&(S.addEventListener("dispose",rt),Ee=new Map,O.programs=Ee,O.programVariants=new Map);let Ye=Ee.get(_e);if(Ye!==void 0){if(O.currentProgram===Ye&&O.lightsStateVersion===Se)return wa(S,ue),Ye}else ue.uniforms=j.getUniforms(S),F!==null&&S.isNodeMaterial&&F.build(S,G,ue),S.onBeforeCompile(ue,C),Ye=j.acquireProgram(ue,_e),Ee.set(_e,Ye),O.uniforms=ue.uniforms;const be=O.uniforms;return(!S.isShaderMaterial&&!S.isRawShaderMaterial||S.clipping===!0)&&(be.clippingPlanes=J.uniform),wa(S,ue),O.needsLights=yl(S),O.lightsStateVersion=Se,O.needsLights&&(be.ambientLightColor.value=k.state.ambient,be.lightProbe.value=k.state.probe,be.sunLights.value=k.state.sun,be.sunLightShadows.value=k.state.sunShadow,be.directionalLights.value=k.state.directional,be.directionalLightShadows.value=k.state.directionalShadow,be.spotLights.value=k.state.spot,be.spotLightShadows.value=k.state.spotShadow,be.rectAreaLights.value=k.state.rectArea,be.ltc_1.value=k.state.rectAreaLTC1,be.ltc_2.value=k.state.rectAreaLTC2,be.pointLights.value=k.state.point,be.pointLightShadows.value=k.state.pointShadow,be.hemisphereLights.value=k.state.hemi,be.sunShadowMatrix.value=k.state.sunShadowMatrix,be.sunShadowCascade.value=k.state.sunShadowCascade,be.directionalShadowMatrix.value=k.state.directionalShadowMatrix,be.spotLightMatrix.value=k.state.spotLightMatrix,be.spotLightMap.value=k.state.spotLightMap,be.pointShadowMatrix.value=k.state.pointShadowMatrix),O.numLightProbeGrids=Ea(A.state.lightProbeGridArray),O.currentProgram=Ye,O.uniformsList=null,Ye}function Sl(S,D,G,O,k){const he=Ce.get(S),Se=he.currentProgram.id+","+O+","+k;let ue=he.programVariants.get(Se);return ue===void 0?(ue=yi(S,D,G),he.programVariants.set(Se,ue)):(he.currentProgram=ue,he.uniformsList=null,he.outputColorSpace=O,he.toneMapping=k),ue}function Ta(S){if(S.uniformsList===null){const D=S.currentProgram.getUniforms();S.uniformsList=_s.seqWithValue(D.seq,S.uniforms)}return S.uniformsList}function wa(S,D){const G=Ce.get(S);G.outputColorSpace=D.outputColorSpace,G.batching=D.batching,G.batchingColor=D.batchingColor,G.instancing=D.instancing,G.instancingColor=D.instancingColor,G.instancingMorph=D.instancingMorph,G.skinning=D.skinning,G.morphTargets=D.morphTargets,G.morphNormals=D.morphNormals,G.morphColors=D.morphColors,G.morphTargetsCount=D.morphTargetsCount,G.numClippingPlanes=D.numClippingPlanes,G.numIntersection=D.numClipIntersection,G.vertexAlphas=D.vertexAlphas,G.vertexTangents=D.vertexTangents,G.toneMapping=D.toneMapping}function Ml(S,D,G,O,k){D.isScene!==!0&&(D=Ft),Pe.resetTextureUnits();const he=D.fog,Se=O.isMeshStandardMaterial||O.isMeshLambertMaterial||O.isMeshPhongMaterial?D.environment:null,ue=ne===null?C.outputColorSpace:ne.isXRRenderTarget===!0?ne.texture.colorSpace:Ze.workingColorSpace,_e=O.isMeshStandardMaterial||O.isMeshLambertMaterial&&!O.envMap||O.isMeshPhongMaterial&&!O.envMap,Ee=w.get(O.envMap||Se,_e),Ue=O.vertexColors===!0&&!!G.attributes.color&&G.attributes.color.itemSize===4,Ye=!!G.attributes.tangent&&(!!O.normalMap||O.anisotropy>0),be=!!G.morphAttributes.position,Ke=!!G.morphAttributes.normal,St=!!G.morphAttributes.color;let ut=wn;O.toneMapped&&(ne===null||ne.isXRRenderTarget===!0)&&(ut=C.toneMapping);const lt=G.morphAttributes.position||G.morphAttributes.normal||G.morphAttributes.color,Lt=lt!==void 0?lt.length:0,ye=Ce.get(O),Bt=A.state.lights;if(xt===!0&&(Pt===!0||S!==ee)){const ft=S===ee&&O.id===q;J.setState(O,S,ft)}let tt=!1,Vn=!1;O.version===ye.__version?ye.needsLights&&ye.lightsStateVersion!==Bt.state.version||k.isBatchedMesh&&ye.batching===!1||!k.isBatchedMesh&&ye.batching===!0||k.isBatchedMesh&&ye.batchingColor===!0&&k._colorsTexture===null||k.isBatchedMesh&&ye.batchingColor===!1&&k._colorsTexture!==null||k.isInstancedMesh&&ye.instancing===!1||!k.isInstancedMesh&&ye.instancing===!0||k.isSkinnedMesh&&ye.skinning===!1||!k.isSkinnedMesh&&ye.skinning===!0||k.isInstancedMesh&&ye.instancingColor===!0&&k.instanceColor===null||k.isInstancedMesh&&ye.instancingColor===!1&&k.instanceColor!==null||k.isInstancedMesh&&ye.instancingMorph===!0&&k.morphTexture===null||k.isInstancedMesh&&ye.instancingMorph===!1&&k.morphTexture!==null||ye.envMap!==Ee||O.fog===!0&&ye.fog!==he||ye.numClippingPlanes!==void 0&&(ye.numClippingPlanes!==J.numPlanes||ye.numIntersection!==J.numIntersection)||ye.vertexAlphas!==Ue||ye.vertexTangents!==Ye||ye.morphTargets!==be||ye.morphNormals!==Ke||ye.morphColors!==St||ye.morphTargetsCount!==Lt||ye.numLightProbeGrids!==Ea(A.state.lightProbeGridArray)?tt=!0:(ye.outputColorSpace!==ue||ye.toneMapping!==ut)&&(Vn=!0):(tt=!0,ye.__version=O.version);let Dt=ye.currentProgram;(tt===!0||Vn===!0)&&(Dt=Vn===!0?Sl(O,D,k,ue,ut):yi(O,D,k),F&&O.isNodeMaterial&&F.onUpdateProgram(O,Dt,ye));let Hi=!1,Cn=!1,Wn=!1;const Je=Dt.getUniforms(),Rt=ye.uniforms;if(le.useProgram(Dt.program)&&(Hi=!0,Cn=!0,Wn=!0),O.id!==q&&(q=O.id,Cn=!0),Hi||ee!==S){le.buffers.depth.getReversed()&&S.reversedDepth!==!0&&(S._reversedDepth=!0,S.updateProjectionMatrix()),Je.setValue(N,"projectionMatrix",S.projectionMatrix),Je.setValue(N,"viewMatrix",S.matrixWorldInverse);const wt=Je.map.cameraPosition;wt!==void 0&&wt.setValue(N,qt.setFromMatrixPosition(S.matrixWorld)),dt.logarithmicDepthBuffer&&Je.setValue(N,"logDepthBufFC",2/(Math.log(S.far+1)/Math.LN2)),(O.isMeshPhongMaterial||O.isMeshToonMaterial||O.isMeshLambertMaterial||O.isMeshBasicMaterial||O.isMeshStandardMaterial||O.isShaderMaterial)&&Je.setValue(N,"isOrthographic",S.isOrthographicCamera===!0),ee!==S&&(ee=S,Cn=!0,Wn=!0)}if(ye.needsLights&&(Bt.state.sunShadowMap.length>0&&Je.setValue(N,"sunShadowMap",Bt.state.sunShadowMap,Pe),Bt.state.directionalShadowMap.length>0&&Je.setValue(N,"directionalShadowMap",Bt.state.directionalShadowMap,Pe),Bt.state.spotShadowMap.length>0&&Je.setValue(N,"spotShadowMap",Bt.state.spotShadowMap,Pe),Bt.state.pointShadowMap.length>0&&Je.setValue(N,"pointShadowMap",Bt.state.pointShadowMap,Pe)),k.isSkinnedMesh){Je.setOptional(N,k,"bindMatrix"),Je.setOptional(N,k,"bindMatrixInverse");const ft=k.skeleton;ft&&(ft.boneTexture===null&&ft.computeBoneTexture(),Je.setValue(N,"boneTexture",ft.boneTexture,Pe))}k.isBatchedMesh&&(Je.setOptional(N,k,"batchingTexture"),Je.setValue(N,"batchingTexture",k._matricesTexture,Pe),Je.setOptional(N,k,"batchingIdTexture"),Je.setValue(N,"batchingIdTexture",k._indirectTexture,Pe),Je.setOptional(N,k,"batchingColorTexture"),k._colorsTexture!==null&&Je.setValue(N,"batchingColorTexture",k._colorsTexture,Pe));const jt=G.morphAttributes;if((jt.position!==void 0||jt.normal!==void 0||jt.color!==void 0)&&Q.update(k,G,Dt),(Cn||ye.receiveShadow!==k.receiveShadow)&&(ye.receiveShadow=k.receiveShadow,Je.setValue(N,"receiveShadow",k.receiveShadow)),(O.isMeshStandardMaterial||O.isMeshLambertMaterial||O.isMeshPhongMaterial)&&O.envMap===null&&D.environment!==null&&(Rt.envMapIntensity.value=D.environmentIntensity),Rt.dfgLUT!==void 0&&(Rt.dfgLUT.value=Yp()),Cn){if(Je.setValue(N,"toneMappingExposure",C.toneMappingExposure),ye.needsLights&&bl(Rt,Wn),he&&O.fog===!0&&ae.refreshFogUniforms(Rt,he),ae.refreshMaterialUniforms(Rt,O,me,Ae,A.state.transmissionRenderTarget[S.id]),ye.needsLights&&ye.numLightProbeGrids>0){const ft=E;let wt=0;for(const _n of A.state.lightProbeGridArray)_n.texture!==null&&(ft.textures[wt]=_n.texture,ft.min[wt]=_n.boundingBox.min,ft.max[wt]=_n.boundingBox.max,ft.resolution[wt]=_n.resolution,wt++);for(const _n in ft)ft[_n].length=wt;Rt.probesSH.value=ft.textures,Rt.probesMin.value=ft.min,Rt.probesMax.value=ft.max,Rt.probesResolution.value=ft.resolution}_s.upload(N,Ta(ye),Rt,Pe)}if(O.isShaderMaterial&&O.uniformsNeedUpdate===!0&&(_s.upload(N,Ta(ye),Rt,Pe),O.uniformsNeedUpdate=!1),O.isSpriteMaterial&&Je.setValue(N,"center",k.center),Je.setValue(N,"modelViewMatrix",k.modelViewMatrix),Je.setValue(N,"normalMatrix",k.normalMatrix),Je.setValue(N,"modelMatrix",k.matrixWorld),O.uniformsGroups!==void 0){const ft=O.uniformsGroups;for(let wt=0,_n=ft.length;wt<_n;wt++){const Ra=ft[wt];Te.update(Ra,Dt),Te.bind(Ra,Dt)}}return Dt}function bl(S,D){S.ambientLightColor.needsUpdate=D,S.lightProbe.needsUpdate=D,S.sunLights.needsUpdate=D,S.sunLightShadows.needsUpdate=D,S.directionalLights.needsUpdate=D,S.directionalLightShadows.needsUpdate=D,S.pointLights.needsUpdate=D,S.pointLightShadows.needsUpdate=D,S.spotLights.needsUpdate=D,S.spotLightShadows.needsUpdate=D,S.rectAreaLights.needsUpdate=D,S.hemisphereLights.needsUpdate=D}function yl(S){return S.isMeshLambertMaterial||S.isMeshToonMaterial||S.isMeshPhongMaterial||S.isMeshStandardMaterial||S.isShadowMaterial||S.isShaderMaterial&&S.lights===!0}this.getActiveCubeFace=function(){return K},this.getActiveMipmapLevel=function(){return H},this.getRenderTarget=function(){return ne},this.setRenderTargetTextures=function(S,D,G){const O=Ce.get(S);O.__autoAllocateDepthBuffer=S.resolveDepthBuffer===!1,O.__autoAllocateDepthBuffer===!1&&(O.__useRenderToTexture=!1),Ce.get(S.texture).__webglTexture=D,Ce.get(S.depthTexture).__webglTexture=O.__autoAllocateDepthBuffer?void 0:G,O.__hasExternalTextures=!0},this.setRenderTargetFramebuffer=function(S,D){const G=Ce.get(S);G.__webglFramebuffer=D,G.__useDefaultFramebuffer=D===void 0},this.setRenderTarget=function(S,D=0,G=0){ne=S,K=D,H=G;let O=null,k=!1,he=!1;if(S){const ue=Ce.get(S);if(ue.__useDefaultFramebuffer!==void 0){le.bindFramebuffer(N.FRAMEBUFFER,ue.__webglFramebuffer),te.copy(S.viewport),Me.copy(S.scissor),Le=S.scissorTest,le.viewport(te),le.scissor(Me),le.setScissorTest(Le),q=-1;return}else if(ue.__webglFramebuffer===void 0)Pe.setupRenderTarget(S);else if(ue.__hasExternalTextures)Pe.rebindTextures(S,Ce.get(S.texture).__webglTexture,Ce.get(S.depthTexture).__webglTexture);else if(S.depthBuffer){const Ue=S.depthTexture;if(ue.__boundDepthTexture!==Ue){if(Ue!==null&&Ce.has(Ue)&&(S.width!==Ue.image.width||S.height!==Ue.image.height))throw new Error("THREE.WebGLRenderer: Attached DepthTexture is initialized to the incorrect size.");Pe.setupDepthRenderbuffer(S)}}const _e=S.texture;(_e.isData3DTexture||_e.isDataArrayTexture||_e.isCompressedArrayTexture)&&(he=!0);const Ee=Ce.get(S).__webglFramebuffer;S.isWebGLCubeRenderTarget?(Array.isArray(Ee[D])?O=Ee[D][G]:O=Ee[D],k=!0):S.samples>0&&Pe.useMultisampledRTT(S)===!1?O=Ce.get(S).__webglMultisampledFramebuffer:Array.isArray(Ee)?O=Ee[G]:O=Ee,te.copy(S.viewport),Me.copy(S.scissor),Le=S.scissorTest}else te.copy(Re).multiplyScalar(me).floor(),Me.copy(et).multiplyScalar(me).floor(),Le=it;if(G!==0&&(O=X),le.bindFramebuffer(N.FRAMEBUFFER,O)&&le.drawBuffers(S,O),le.viewport(te),le.scissor(Me),le.setScissorTest(Le),k){const ue=Ce.get(S.texture);N.framebufferTexture2D(N.FRAMEBUFFER,N.COLOR_ATTACHMENT0,N.TEXTURE_CUBE_MAP_POSITIVE_X+D,ue.__webglTexture,G)}else if(he){const ue=D;for(let _e=0;_e<S.textures.length;_e++){const Ee=Ce.get(S.textures[_e]);N.framebufferTextureLayer(N.FRAMEBUFFER,N.COLOR_ATTACHMENT0+_e,Ee.__webglTexture,G,ue)}}else if(S!==null&&G!==0){const ue=Ce.get(S.texture);N.framebufferTexture2D(N.FRAMEBUFFER,N.COLOR_ATTACHMENT0,N.TEXTURE_2D,ue.__webglTexture,G)}q=-1};function Aa(S){const D=Ce.get(S);return(D.__readFormat!==S.format||D.__readType!==S.type)&&(D.__readFormat=S.format,D.__readType=S.type,D.__formatReadable=dt.textureFormatReadable(S.format),D.__typeReadable=dt.textureTypeReadable(S.type)),D}this.readRenderTargetPixels=function(S,D,G,O,k,he,Se,ue=0){if(!(S&&S.isWebGLRenderTarget)){nt("WebGLRenderer.readRenderTargetPixels: renderTarget is not THREE.WebGLRenderTarget.");return}let _e=Ce.get(S).__webglFramebuffer;if(S.isWebGLCubeRenderTarget&&Se!==void 0&&(_e=_e[Se],Array.isArray(_e)&&(_e=_e[0])),_e){le.bindFramebuffer(N.FRAMEBUFFER,_e);try{const Ee=S.textures[ue],Ue=Ee.format,Ye=Ee.type;S.textures.length>1&&N.readBuffer(N.COLOR_ATTACHMENT0+ue);const be=Aa(Ee);if(be.__formatReadable===!1){nt("WebGLRenderer.readRenderTargetPixels: renderTarget is not in RGBA or implementation defined format.");return}if(be.__typeReadable===!1){nt("WebGLRenderer.readRenderTargetPixels: renderTarget is not in UnsignedByteType or implementation defined type.");return}D>=0&&D<=S.width-O&&G>=0&&G<=S.height-k&&N.readPixels(D,G,O,k,re.convert(Ue),re.convert(Ye),he)}finally{const Ee=ne!==null?Ce.get(ne).__webglFramebuffer:null;le.bindFramebuffer(N.FRAMEBUFFER,Ee)}}},this.readRenderTargetPixelsAsync=async function(S,D,G,O,k,he,Se,ue=0){if(!(S&&S.isWebGLRenderTarget))throw new Error("THREE.WebGLRenderer.readRenderTargetPixels: renderTarget is not THREE.WebGLRenderTarget.");let _e=Ce.get(S).__webglFramebuffer;if(S.isWebGLCubeRenderTarget&&Se!==void 0&&(_e=_e[Se],Array.isArray(_e)&&(_e=_e[0])),_e)if(D>=0&&D<=S.width-O&&G>=0&&G<=S.height-k){le.bindFramebuffer(N.FRAMEBUFFER,_e);const Ee=S.textures[ue],Ue=Ee.format,Ye=Ee.type;S.textures.length>1&&N.readBuffer(N.COLOR_ATTACHMENT0+ue);const be=Aa(Ee);if(be.__formatReadable===!1)throw new Error("THREE.WebGLRenderer.readRenderTargetPixelsAsync: renderTarget is not in RGBA or implementation defined format.");if(be.__typeReadable===!1)throw new Error("THREE.WebGLRenderer.readRenderTargetPixelsAsync: renderTarget is not in UnsignedByteType or implementation defined type.");const Ke=N.createBuffer();N.bindBuffer(N.PIXEL_PACK_BUFFER,Ke),N.bufferData(N.PIXEL_PACK_BUFFER,he.byteLength,N.STREAM_READ),N.readPixels(D,G,O,k,re.convert(Ue),re.convert(Ye),0),N.bindBuffer(N.PIXEL_PACK_BUFFER,null);const St=ne!==null?Ce.get(ne).__webglFramebuffer:null;le.bindFramebuffer(N.FRAMEBUFFER,St);const ut=N.fenceSync(N.SYNC_GPU_COMMANDS_COMPLETE,0);return N.flush(),await dc(N,ut,4),N.bindBuffer(N.PIXEL_PACK_BUFFER,Ke),N.getBufferSubData(N.PIXEL_PACK_BUFFER,0,he),N.bindBuffer(N.PIXEL_PACK_BUFFER,null),N.deleteBuffer(Ke),N.deleteSync(ut),he}else throw new Error("THREE.WebGLRenderer.readRenderTargetPixelsAsync: requested read bounds are out of range.")},this.copyFramebufferToTexture=function(S,D=null,G=0){const O=Math.pow(2,-G),k=Math.floor(S.image.width*O),he=Math.floor(S.image.height*O),Se=D!==null?D.x:0,ue=D!==null?D.y:0;Pe.setTexture2D(S,0),N.copyTexSubImage2D(N.TEXTURE_2D,G,0,0,Se,ue,k,he),le.unbindTexture()},this.copyTextureToTexture=function(S,D,G=null,O=null,k=0,he=0){let Se,ue,_e,Ee,Ue,Ye,be,Ke,St;const ut=S.isCompressedTexture?S.mipmaps[he]:S.image;if(G!==null)Se=G.max.x-G.min.x,ue=G.max.y-G.min.y,_e=G.isBox3?G.max.z-G.min.z:1,Ee=G.min.x,Ue=G.min.y,Ye=G.isBox3?G.min.z:0;else{const Je=Math.pow(2,-k);Se=Math.floor(ut.width*Je),ue=Math.floor(ut.height*Je),S.isDataArrayTexture?_e=ut.depth:S.isData3DTexture?_e=Math.floor(ut.depth*Je):_e=1,Ee=0,Ue=0,Ye=0}O!==null?(be=O.x,Ke=O.y,St=O.z):(be=0,Ke=0,St=0);const lt=re.convert(D.format),Lt=re.convert(D.type);let ye;D.isData3DTexture?(Pe.setTexture3D(D,0),ye=N.TEXTURE_3D):D.isDataArrayTexture||D.isCompressedArrayTexture?(Pe.setTexture2DArray(D,0),ye=N.TEXTURE_2D_ARRAY):(Pe.setTexture2D(D,0),ye=N.TEXTURE_2D),le.activeTexture(N.TEXTURE0),le.pixelStorei(N.UNPACK_FLIP_Y_WEBGL,D.flipY),le.pixelStorei(N.UNPACK_PREMULTIPLY_ALPHA_WEBGL,D.premultiplyAlpha),le.pixelStorei(N.UNPACK_ALIGNMENT,D.unpackAlignment);const Bt=le.getParameter(N.UNPACK_ROW_LENGTH),tt=le.getParameter(N.UNPACK_IMAGE_HEIGHT),Vn=le.getParameter(N.UNPACK_SKIP_PIXELS),Dt=le.getParameter(N.UNPACK_SKIP_ROWS),Hi=le.getParameter(N.UNPACK_SKIP_IMAGES);le.pixelStorei(N.UNPACK_ROW_LENGTH,ut.width),le.pixelStorei(N.UNPACK_IMAGE_HEIGHT,ut.height),le.pixelStorei(N.UNPACK_SKIP_PIXELS,Ee),le.pixelStorei(N.UNPACK_SKIP_ROWS,Ue),le.pixelStorei(N.UNPACK_SKIP_IMAGES,Ye);const Cn=S.isDataArrayTexture||S.isData3DTexture,Wn=D.isDataArrayTexture||D.isData3DTexture;if(S.isDepthTexture){const Je=Ce.get(S),Rt=Ce.get(D),jt=Ce.get(Je.__renderTarget),ft=Ce.get(Rt.__renderTarget);le.bindFramebuffer(N.READ_FRAMEBUFFER,jt.__webglFramebuffer),le.bindFramebuffer(N.DRAW_FRAMEBUFFER,ft.__webglFramebuffer);for(let wt=0;wt<_e;wt++)Cn&&(N.framebufferTextureLayer(N.READ_FRAMEBUFFER,N.COLOR_ATTACHMENT0,Ce.get(S).__webglTexture,k,Ye+wt),N.framebufferTextureLayer(N.DRAW_FRAMEBUFFER,N.COLOR_ATTACHMENT0,Ce.get(D).__webglTexture,he,St+wt)),N.blitFramebuffer(Ee,Ue,Ee+Se,Ue+ue,be,Ke,be+Se,Ke+ue,N.DEPTH_BUFFER_BIT,N.NEAREST);le.bindFramebuffer(N.READ_FRAMEBUFFER,null),le.bindFramebuffer(N.DRAW_FRAMEBUFFER,null)}else if(k!==0||S.isRenderTargetTexture||Ce.has(S)){const Je=Ce.get(S),Rt=Ce.get(D);le.bindFramebuffer(N.READ_FRAMEBUFFER,L),le.bindFramebuffer(N.DRAW_FRAMEBUFFER,W);for(let jt=0;jt<_e;jt++)Cn?N.framebufferTextureLayer(N.READ_FRAMEBUFFER,N.COLOR_ATTACHMENT0,Je.__webglTexture,k,Ye+jt):N.framebufferTexture2D(N.READ_FRAMEBUFFER,N.COLOR_ATTACHMENT0,N.TEXTURE_2D,Je.__webglTexture,k),Wn?N.framebufferTextureLayer(N.DRAW_FRAMEBUFFER,N.COLOR_ATTACHMENT0,Rt.__webglTexture,he,St+jt):N.framebufferTexture2D(N.DRAW_FRAMEBUFFER,N.COLOR_ATTACHMENT0,N.TEXTURE_2D,Rt.__webglTexture,he),k!==0?N.blitFramebuffer(Ee,Ue,Ee+Se,Ue+ue,be,Ke,be+Se,Ke+ue,N.COLOR_BUFFER_BIT,N.NEAREST):Wn?N.copyTexSubImage3D(ye,he,be,Ke,St+jt,Ee,Ue,Se,ue):N.copyTexSubImage2D(ye,he,be,Ke,Ee,Ue,Se,ue);le.bindFramebuffer(N.READ_FRAMEBUFFER,null),le.bindFramebuffer(N.DRAW_FRAMEBUFFER,null)}else Wn?S.isDataTexture||S.isData3DTexture?N.texSubImage3D(ye,he,be,Ke,St,Se,ue,_e,lt,Lt,ut.data):D.isCompressedArrayTexture?N.compressedTexSubImage3D(ye,he,be,Ke,St,Se,ue,_e,lt,ut.data):N.texSubImage3D(ye,he,be,Ke,St,Se,ue,_e,lt,Lt,ut):S.isDataTexture?N.texSubImage2D(N.TEXTURE_2D,he,be,Ke,Se,ue,lt,Lt,ut.data):S.isCompressedTexture?N.compressedTexSubImage2D(N.TEXTURE_2D,he,be,Ke,ut.width,ut.height,lt,ut.data):N.texSubImage2D(N.TEXTURE_2D,he,be,Ke,Se,ue,lt,Lt,ut);le.pixelStorei(N.UNPACK_ROW_LENGTH,Bt),le.pixelStorei(N.UNPACK_IMAGE_HEIGHT,tt),le.pixelStorei(N.UNPACK_SKIP_PIXELS,Vn),le.pixelStorei(N.UNPACK_SKIP_ROWS,Dt),le.pixelStorei(N.UNPACK_SKIP_IMAGES,Hi),he===0&&D.generateMipmaps===!0&&D.mipmapsAutoUpdate===!0&&N.generateMipmap(ye),le.unbindTexture()},this.initRenderTarget=function(S){Ce.get(S).__webglFramebuffer===void 0&&Pe.setupRenderTarget(S)},this.initTexture=function(S){S.isCubeTexture?Pe.setTextureCube(S,0):S.isData3DTexture?Pe.setTexture3D(S,0):S.isDataArrayTexture||S.isCompressedArrayTexture?Pe.setTexture2DArray(S,0):Pe.setTexture2D(S,0),le.unbindTexture()},this.resetState=function(){K=0,H=0,ne=null,le.reset(),oe.reset()},typeof __THREE_DEVTOOLS__<"u"&&__THREE_DEVTOOLS__.dispatchEvent(new CustomEvent("observe",{detail:this}))}get coordinateSystem(){return ln}get outputColorSpace(){return this._outputColorSpace}set outputColorSpace(e){this._outputColorSpace=e;const t=this.getContext();t.drawingBufferColorSpace=Ze._getDrawingBufferColorSpace(e),t.unpackColorSpace=Ze._getUnpackColorSpace()}}const Zp={__name:"EducativeHeroScene",setup(i){const e=us(null);let t=null,n=null,s=null,r=null,a=null,o=0,d=0,c=0,m=0,x=null,f=null,g=null,l=null,T=null,p=null,u=null,b=[];function y(){if(!e.value)return;const h=e.value.clientWidth||480,M=e.value.clientHeight||420;s=new Cc,a=new jc,r=new Qt(40,h/M,.1,100),r.position.set(0,1.2,5.5),r.lookAt(0,0,0),n=new Kp({antialias:!0,alpha:!0,powerPreference:"high-performance"}),n.setSize(h,M),n.setPixelRatio(Math.min(window.devicePixelRatio||1,2)),n.toneMapping=jr,n.toneMappingExposure=1.15,e.value.appendChild(n.domElement);const I=new Jc(988970,2.5);s.add(I);const C=new io(16775149,3.2);C.position.set(4,5,4),s.add(C);const U=new io(62206,3);U.position.set(-4,-2,-3),s.add(U);const F=new Zc(16096779,2.8,10);F.position.set(0,1.5,1),s.add(F),x=new kn,s.add(x),f=new kn,f.position.set(0,-.3,0),f.rotation.x=.35,x.add(f);const X=new Es(.08,.08,1.6,16,1,!1,0,Math.PI),L=new rn({color:988970,roughness:.3,metalness:.6}),W=new ht(X,L);W.rotation.z=Math.PI/2,W.rotation.y=Math.PI/2,f.add(W);const K=new an(1.05,.04,1.55),H=new rn({color:1976635,roughness:.25,metalness:.4}),ne=new ht(K,H);ne.position.set(-.55,-.02,0),ne.rotation.z=.22,f.add(ne);const q=new ht(K,H);q.position.set(.55,-.02,0),q.rotation.z=-.22,f.add(q);const ee=new rn({color:16317180,roughness:.4,emissive:165063,emissiveIntensity:.08}),te=new an(.96,.1,1.45);g=new ht(te,ee),g.position.set(-.5,.04,0),g.rotation.z=.18,f.add(g),l=new ht(te,ee),l.position.set(.5,.04,0),l.rotation.z=-.18,f.add(l);const Me=new an(.04,.09,1.46),Le=new rn({color:16096779,roughness:.2,metalness:.8}),$=new ht(Me,Le);$.position.set(-.99,.12,0),$.rotation.z=.18,f.add($);const pe=new ht(Me,Le);pe.position.set(.99,.12,0),pe.rotation.z=-.18,f.add(pe);const ve=new rn({color:16347926,roughness:.4,metalness:.2}),Ae=new an(.08,.02,1.1),me=new ht(Ae,ve);me.position.set(.05,.08,.35),me.rotation.x=.1,f.add(me),T=new kn,T.position.set(0,.35,0),x.add(T);const Ie=new ws(1.4,.022,16,80),gt=new rn({color:62206,emissive:62206,emissiveIntensity:.6,roughness:.2,metalness:.8}),Re=new ht(Ie,gt);Re.rotation.x=Math.PI/3.2,Re.rotation.y=Math.PI/6,T.add(Re);const et=new ws(1.65,.016,16,80),it=new rn({color:8490232,emissive:6514417,emissiveIntensity:.4,roughness:.3,metalness:.7}),Ge=new ht(et,it);Ge.rotation.x=-Math.PI/3.6,Ge.rotation.y=-Math.PI/4,T.add(Ge);const bt=new Ts(.12,24,24),xt=new rn({color:16772565,emissive:16096779,emissiveIntensity:1.8,roughness:.1});p=new ht(bt,xt),T.add(p),u=new kn,u.position.set(0,.8,0),u.scale.set(.65,.65,.65),x.add(u);const Pt=new an(.85,.035,.85),ct=new rn({color:988970,roughness:.3,metalness:.3}),qt=new ht(Pt,ct);qt.rotation.y=Math.PI/4,u.add(qt);const vt=new Es(.26,.28,.2,16),Ft=new ht(vt,ct);Ft.position.set(0,-.1,0),u.add(Ft);const Ot=new rn({color:16498468,emissive:14251782,emissiveIntensity:.5,roughness:.2,metalness:.8}),Gt=new Ts(.04,12,12),N=new ht(Gt,Ot);N.position.set(0,.03,0),u.add(N);const Ht=new an(.015,.015,.38),ot=new ht(Ht,Ot);ot.position.set(.18,.02,.18),ot.rotation.y=Math.PI/4,u.add(ot);const dt=new ua(.09,0),le=[62206,16347926,11032055,1096065,3718648];for(let st=0;st<7;st++){const Ce=new rn({color:le[st%le.length],emissive:le[st%le.length],emissiveIntensity:.8,roughness:.2,metalness:.9}),Pe=new ht(dt,Ce),w=st/7*Math.PI*2,v=1.8+st%2*.4;Pe.position.set(Math.cos(w)*v,-.3+st%3*.5,Math.sin(w)*v),x.add(Pe),b.push({mesh:Pe,baseY:Pe.position.y,speed:.8+st*.2,offset:st*.9})}window.addEventListener("resize",R),window.addEventListener("mousemove",E),A()}function E(h){if(!e.value)return;const M=e.value.getBoundingClientRect(),I=h.clientX-M.left-M.width/2,C=h.clientY-M.top-M.height/2;c=I/M.width*.6,m=-(C/M.height)*.6}function R(){if(!e.value||!n||!r)return;const h=e.value.clientWidth,M=e.value.clientHeight;r.aspect=h/M,r.updateProjectionMatrix(),n.setSize(h,M)}function A(){if(t=requestAnimationFrame(A),!a||!n||!s||!r)return;const h=Math.min(a.getDelta(),.05),M=a.getElapsedTime();if(o+=(c-o)*.05,d+=(m-d)*.05,x&&(x.rotation.y=M*.25+o*.8,x.rotation.x=d*.5+Math.sin(M*.8)*.05,x.position.y=Math.sin(M*1.4)*.12),f&&(f.rotation.y=Math.sin(M*.6)*.08),g&&l&&(g.rotation.z=.18+Math.sin(M*2.2)*.02,l.rotation.z=-.18-Math.sin(M*2.2+.4)*.02),p){const I=M*1.8,C=1.4;p.position.set(Math.cos(I)*C,Math.sin(I)*.6+.1,Math.sin(I)*C)}u&&(u.position.y=.85+Math.sin(M*1.6+.8)*.08,u.rotation.y=Math.sin(M*.9)*.2),b.forEach(I=>{I.mesh.position.y=I.baseY+Math.sin(M*I.speed+I.offset)*.18,I.mesh.rotation.x+=h*.8,I.mesh.rotation.y+=h*1.2}),n.render(s,r)}return Ao(()=>{window.matchMedia("(prefers-reduced-motion: reduce)").matches||y()}),Ro(()=>{t&&cancelAnimationFrame(t),window.removeEventListener("resize",R),window.removeEventListener("mousemove",E),s&&s.traverse(h=>{h.isMesh&&(h.geometry&&h.geometry.dispose(),h.material&&(Array.isArray(h.material)?h.material.forEach(M=>M.dispose()):h.material.dispose()))}),n&&(n.dispose(),n.domElement&&n.domElement.parentNode&&n.domElement.parentNode.removeChild(n.domElement))}),(h,M)=>(We(),qe("div",{ref_key:"container",ref:e,class:"relative w-full h-[380px] sm:h-[440px] md:h-[480px] flex items-center justify-center pointer-events-auto select-none","aria-label":"Interactive 3D Knowledge Universe"},[...M[0]||(M[0]=[El('<div class="pointer-events-none absolute inset-0 -z-10 flex items-center justify-center"><div class="w-72 h-72 sm:w-88 sm:h-88 rounded-full bg-gradient-to-tr from-[#00f2fe]/20 via-[#6366f1]/20 to-[#f59e0b]/20 blur-3xl opacity-75"></div></div><div class="pointer-events-none absolute top-4 right-4 sm:top-8 sm:right-6 px-3 py-1.5 rounded-xl bg-[#0f172a]/80 border border-cyan-400/30 backdrop-blur text-[11px] font-bold text-cyan-300 shadow-lg flex items-center gap-1.5 animate-bounce"><span class="w-2 h-2 rounded-full bg-cyan-400"></span><span>AI বুক রিডার ও স্মার্ট চ্যাট</span></div><div class="pointer-events-none absolute bottom-6 left-4 sm:bottom-8 sm:left-6 px-3 py-1.5 rounded-xl bg-[#0f172a]/80 border border-amber-400/30 backdrop-blur text-[11px] font-bold text-amber-300 shadow-lg flex items-center gap-1.5"><span>🎯</span><span>২০,০০০+ সলভড প্রশ্নব্যাংক</span></div><div class="pointer-events-none absolute bottom-4 right-6 sm:bottom-6 sm:right-10 px-3 py-1.5 rounded-xl bg-[#0f172a]/80 border border-indigo-400/30 backdrop-blur text-[11px] font-bold text-indigo-300 shadow-lg flex items-center gap-1.5"><span>⚡</span><span>ভুল উত্তরের স্বয়ংক্রিয় খাতা</span></div>',4)])],512))}},$p={class:"home-design relative isolate overflow-hidden bg-[var(--bg)]"},Jp={class:"max-w-7xl mx-auto px-4 md:px-6 pt-10 md:pt-14 pb-16 md:pb-20"},Qp={class:"grid grid-cols-1 lg:grid-cols-12 gap-10 xl:gap-14 items-center"},jp={class:"lg:col-span-7 flex flex-col items-start"},em={class:"inline-flex items-center gap-2.5 rounded-full border border-white/5 bg-[#1c1f2a]/80 px-4 py-2 shadow-sm backdrop-blur"},tm={class:"font-mono text-[10px] md:text-[11px] font-semibold uppercase tracking-[.11em] text-[var(--primary)]"},nm={class:"mt-6 font-heading text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight leading-[1.08] text-[var(--text)]"},im={class:"mt-5 max-w-xl text-base md:text-lg leading-relaxed text-[var(--text-muted)]"},sm={class:"mt-8 flex flex-wrap items-center gap-3 w-full sm:w-auto"},rm={class:"lg:col-span-5 relative"},am={class:"relative overflow-hidden rounded-3xl border border-cyan-500/20 bg-gradient-to-b from-[#131b2e]/95 via-[#0d1322] to-[#0a0e1a] p-3 sm:p-5 shadow-2xl backdrop-blur"},om={class:"grid grid-cols-2 gap-2 text-center text-xs"},lm={class:"p-3 rounded-2xl bg-white/[0.03] border border-white/5"},cm={class:"font-heading text-lg font-black text-cyan-400"},dm={class:"text-[10px] text-gray-400 mt-0.5"},um={class:"p-3 rounded-2xl bg-white/[0.03] border border-white/5"},fm={class:"font-heading text-lg font-black text-amber-400"},hm={class:"text-[10px] text-gray-400 mt-0.5"},pm={id:"what-we-do",class:"max-w-7xl mx-auto px-4 md:px-6 py-12 md:py-16 scroll-mt-20"},mm={class:"text-center max-w-3xl mx-auto mb-12"},gm={class:"inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-cyan-500/10 border border-cyan-500/25 text-cyan-400 text-xs font-bold uppercase tracking-wider mb-3"},xm={class:"text-3xl sm:text-4xl lg:text-5xl font-black font-heading text-white tracking-tight"},_m={class:"mt-4 text-base md:text-lg text-gray-400 leading-relaxed"},vm={class:"grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5"},Sm={class:"group relative overflow-hidden rounded-3xl border border-white/10 bg-gradient-to-b from-[#182138] to-[#0e1424] p-6 hover:border-cyan-500/40 transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-cyan-500/10 flex flex-col justify-between"},Mm={class:"text-xl font-bold font-heading text-white mt-1 group-hover:text-cyan-300 transition-colors"},bm={class:"text-xs sm:text-sm text-gray-400 mt-2.5 leading-relaxed"},ym={class:"group relative overflow-hidden rounded-3xl border border-white/10 bg-gradient-to-b from-[#182138] to-[#0e1424] p-6 hover:border-amber-500/40 transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-amber-500/10 flex flex-col justify-between"},Em={class:"text-xl font-bold font-heading text-white mt-1 group-hover:text-amber-300 transition-colors"},Tm={class:"text-xs sm:text-sm text-gray-400 mt-2.5 leading-relaxed"},wm={class:"group relative overflow-hidden rounded-3xl border border-white/10 bg-gradient-to-b from-[#182138] to-[#0e1424] p-6 hover:border-rose-500/40 transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-rose-500/10 flex flex-col justify-between"},Am={class:"text-xl font-bold font-heading text-white mt-1 group-hover:text-rose-300 transition-colors"},Rm={class:"text-xs sm:text-sm text-gray-400 mt-2.5 leading-relaxed"},Cm={class:"group relative overflow-hidden rounded-3xl border border-white/10 bg-gradient-to-b from-[#182138] to-[#0e1424] p-6 hover:border-emerald-500/40 transition-all duration-300 hover:-translate-y-1 hover:shadow-xl hover:shadow-emerald-500/10 flex flex-col justify-between"},Pm={class:"text-xl font-bold font-heading text-white mt-1 group-hover:text-emerald-300 transition-colors"},Lm={class:"text-xs sm:text-sm text-gray-400 mt-2.5 leading-relaxed"},Dm={class:"mt-10 overflow-hidden rounded-3xl border border-cyan-500/30 bg-gradient-to-br from-[#10172a] via-[#0d1424] to-[#151226] p-6 sm:p-8 shadow-2xl"},Im={class:"mt-6 grid grid-cols-1 lg:grid-cols-12 gap-6 items-start"},Nm={class:"lg:col-span-7 space-y-4"},Um={class:"grid grid-cols-1 sm:grid-cols-2 gap-3"},Fm={key:0,class:"text-rose-400 text-xs font-bold"},Om={key:0,class:"text-emerald-400 text-xs font-bold"},Bm={key:1,class:"text-emerald-400 text-xs font-bold"},km={key:0,class:"text-rose-400 text-xs font-bold"},zm={key:0,class:"text-rose-400 text-xs font-bold"},Gm={class:"lg:col-span-5"},Hm={class:"h-full rounded-2xl border border-indigo-500/30 bg-[#161b33]/90 p-5 relative overflow-hidden flex flex-col justify-between"},Vm={key:0,class:"text-xs sm:text-sm text-indigo-100 leading-relaxed space-y-2"},Wm={key:1,class:"text-xs text-gray-400 py-6 text-center"},Xm={class:"mt-4 pt-3 border-t border-white/5 flex items-center justify-between"},qm={id:"admissions-season",class:"max-w-7xl mx-auto px-4 md:px-6 py-10 md:py-14 scroll-mt-24"},Ym={class:"relative overflow-hidden rounded-3xl border border-orange-500/25 bg-gradient-to-br from-[#11192e] via-[#0d1424] to-[#161226] text-white p-6 sm:p-10 shadow-2xl"},Km={class:"relative z-10"},Zm={class:"flex flex-col lg:flex-row lg:items-center justify-between gap-4 pb-6 border-b border-white/10"},$m={class:"inline-flex items-center gap-2 px-3 py-1 rounded-full bg-orange-500/15 border border-orange-500/30 text-orange-400 text-xs font-bold uppercase tracking-wider mb-2"},Jm={class:"text-2xl sm:text-3xl lg:text-4xl font-extrabold font-heading text-white tracking-tight"},Qm={class:"mt-2 text-sm md:text-base text-gray-300 max-w-2xl leading-relaxed"},jm={class:"flex flex-wrap items-center gap-3"},e0={class:"mt-8 grid grid-cols-1 md:grid-cols-2 gap-5"},t0={class:"p-6 rounded-2xl bg-[#141d33]/90 border border-blue-500/30 hover:border-blue-400/60 transition shadow-lg relative overflow-hidden group"},n0={class:"py-3 px-4 rounded-xl bg-[#0d1424] border border-blue-400/20 flex items-center justify-around font-mono text-center mb-4"},i0={class:"text-xl sm:text-2xl font-black text-white"},s0={class:"text-xl sm:text-2xl font-black text-white"},r0={class:"text-xl sm:text-2xl font-black text-white"},a0={class:"text-xl sm:text-2xl font-black text-rose-400"},o0={class:"space-y-2"},l0={class:"grid grid-cols-2 sm:grid-cols-4 gap-2"},c0={class:"mt-4 pt-3 border-t border-white/10 flex items-center justify-between text-xs"},d0={class:"p-6 rounded-2xl bg-[#141d33]/90 border border-purple-500/30 hover:border-purple-400/60 transition shadow-lg relative overflow-hidden group"},u0={class:"py-3 px-4 rounded-xl bg-[#0d1424] border border-purple-400/20 flex items-center justify-around font-mono text-center mb-4"},f0={class:"text-xl sm:text-2xl font-black text-white"},h0={class:"text-xl sm:text-2xl font-black text-white"},p0={class:"text-xl sm:text-2xl font-black text-white"},m0={class:"text-xl sm:text-2xl font-black text-rose-400"},g0={class:"space-y-2"},x0={class:"grid grid-cols-3 gap-2"},_0={class:"mt-4 pt-3 border-t border-white/10 flex items-center justify-between text-xs"},v0={class:"mt-6 pt-5 border-t border-white/10 flex flex-wrap items-center justify-between gap-3 text-xs"},S0={class:"text-gray-400 font-semibold"},M0={class:"flex flex-wrap items-center gap-2"},b0={id:"tools",class:"max-w-7xl mx-auto px-4 md:px-6 py-12 md:py-16 scroll-mt-24"},y0={class:"mb-8 flex flex-col md:flex-row md:items-end justify-between gap-4"},E0={class:"font-mono text-[10px] font-bold uppercase tracking-[.16em] text-amber-500 dark:text-amber-400"},T0={class:"mt-2 font-heading text-3xl md:text-4xl font-bold tracking-tight text-gray-900 dark:text-white"},w0={class:"mt-2 max-w-2xl text-sm leading-relaxed text-[var(--text-muted)]"},A0={class:"flex items-center gap-3"},R0={class:"grid grid-cols-1 md:grid-cols-3 gap-5"},C0={class:"font-heading text-lg font-bold text-gray-900 dark:text-white group-hover:text-amber-500 dark:group-hover:text-amber-400 transition"},P0={class:"mt-2 text-xs text-gray-600 dark:text-gray-400 leading-relaxed"},L0={class:"mt-4 inline-flex items-center gap-1 text-xs font-bold text-amber-500 dark:text-amber-400 group-hover:translate-x-1 transition-transform"},D0={class:"font-heading text-lg font-bold text-gray-900 dark:text-white group-hover:text-blue-500 dark:group-hover:text-blue-400 transition"},I0={class:"mt-2 text-xs text-gray-600 dark:text-gray-400 leading-relaxed"},N0={class:"mt-4 inline-flex items-center gap-1 text-xs font-bold text-blue-500 dark:text-blue-400 group-hover:translate-x-1 transition-transform"},U0={class:"font-heading text-lg font-bold text-gray-900 dark:text-white group-hover:text-emerald-500 dark:group-hover:text-emerald-400 transition"},F0={class:"mt-2 text-xs text-gray-600 dark:text-gray-400 leading-relaxed"},O0={class:"mt-4 inline-flex items-center gap-1 text-xs font-bold text-emerald-500 dark:text-emerald-400 group-hover:translate-x-1 transition-transform"},B0={id:"jobs",class:"max-w-7xl mx-auto px-4 md:px-6 py-12 md:py-16 scroll-mt-24 border-t border-gray-200 dark:border-white/5"},k0={class:"mb-8 flex flex-col md:flex-row md:items-end justify-between gap-4"},z0={class:"font-mono text-[10px] font-bold uppercase tracking-[.16em] text-emerald-500 dark:text-emerald-400"},G0={class:"mt-2 font-heading text-3xl md:text-4xl font-bold tracking-tight text-gray-900 dark:text-white"},H0={class:"mt-2 max-w-2xl text-sm leading-relaxed text-[var(--text-muted)]"},V0={key:0,class:"grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4"},W0={class:"flex items-center justify-between text-[11px] mb-2"},X0={class:"px-2 py-0.5 rounded-full bg-blue-500/15 text-blue-600 dark:text-blue-400 font-bold uppercase"},q0={class:"text-rose-500 dark:text-rose-400 font-semibold text-[10px]"},Y0={class:"text-xs font-semibold text-[var(--primary)] truncate block mb-1"},K0={class:"font-heading text-sm font-bold text-gray-900 dark:text-white line-clamp-2 hover:text-[var(--primary)] transition"},Z0={class:"mt-3 text-[11px] text-gray-600 dark:text-gray-400 space-y-1"},$0={key:0},J0={key:1,class:"truncate"},Q0={class:"mt-4 pt-3 border-t border-gray-100 dark:border-white/5 flex items-center justify-between text-xs"},j0=["href"],eg={key:1,class:"text-center py-8 rounded-2xl bg-gray-50 dark:bg-[#171c28] border border-gray-200 dark:border-white/5 text-xs text-gray-500 dark:text-gray-400"},tg={id:"bundles",class:"max-w-7xl mx-auto px-4 md:px-6 py-14 md:py-20 scroll-mt-24 border-t border-gray-200 dark:border-white/5"},ng={class:"mx-auto mb-10 max-w-3xl text-center"},ig={class:"inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[var(--primary)]/15 border border-[var(--primary)]/30 text-[var(--primary)] text-xs font-bold uppercase tracking-wider mb-3"},sg={class:"font-heading text-3xl md:text-5xl font-extrabold text-gray-900 dark:text-white tracking-tight"},rg={class:"mt-3 text-sm md:text-base leading-relaxed text-[var(--text-muted)]"},ag={class:"grid grid-cols-1 lg:grid-cols-2 gap-8 items-stretch"},og={class:"rounded-3xl border border-amber-500/30 bg-white dark:bg-gradient-to-b dark:from-[#1d1f2e] dark:via-[#161a26] dark:to-[#0e121d] p-7 md:p-9 shadow-xl relative flex flex-col justify-between overflow-hidden"},lg={class:"flex items-center justify-between gap-3 mb-4"},cg={class:"px-3 py-1 rounded-full bg-amber-500/15 text-amber-600 dark:text-amber-300 font-mono text-xs font-bold uppercase tracking-wider"},dg={class:"font-heading text-2xl md:text-3xl font-extrabold text-gray-900 dark:text-white"},ug={class:"mt-2 text-xs md:text-sm text-gray-600 dark:text-gray-300 leading-relaxed"},fg={class:"mt-6 space-y-3 text-xs md:text-sm text-gray-700 dark:text-gray-200"},hg={class:"flex items-start gap-2.5"},pg={class:"flex items-start gap-2.5"},mg={class:"flex items-start gap-2.5"},gg={class:"flex items-start gap-2.5"},xg={class:"mt-8 pt-6 border-t border-gray-200 dark:border-white/10 flex flex-col sm:flex-row items-center justify-between gap-4"},_g={class:"text-xs text-amber-600 dark:text-amber-400 font-bold block"},vg={class:"font-heading text-lg font-bold text-gray-900 dark:text-white"},Sg={class:"rounded-3xl border border-emerald-500/30 bg-white dark:bg-gradient-to-b dark:from-[#162524] dark:via-[#121c22] dark:to-[#0e121d] p-7 md:p-9 shadow-xl relative flex flex-col justify-between overflow-hidden"},Mg={class:"flex items-center justify-between gap-3 mb-4"},bg={class:"px-3 py-1 rounded-full bg-emerald-500/15 text-emerald-600 dark:text-emerald-300 font-mono text-xs font-bold uppercase tracking-wider"},yg={class:"font-heading text-2xl md:text-3xl font-extrabold text-gray-900 dark:text-white"},Eg={class:"mt-2 text-xs md:text-sm text-gray-600 dark:text-gray-300 leading-relaxed"},Tg={class:"mt-6 space-y-3 text-xs md:text-sm text-gray-700 dark:text-gray-200"},wg={class:"flex items-start gap-2.5"},Ag={class:"flex items-start gap-2.5"},Rg={class:"flex items-start gap-2.5"},Cg={class:"flex items-start gap-2.5"},Pg={class:"mt-8 pt-6 border-t border-gray-200 dark:border-white/10 flex flex-col sm:flex-row items-center justify-between gap-4"},Lg={class:"text-xs text-emerald-600 dark:text-emerald-400 font-bold block"},Dg={class:"font-heading text-lg font-bold text-gray-900 dark:text-white"},Ig={id:"courses",class:"max-w-7xl mx-auto px-4 md:px-6 py-14 md:py-20 scroll-mt-24 border-t border-white/5"},Ng={class:"mb-8 flex flex-col md:flex-row md:items-end justify-between gap-4"},Ug={class:"font-mono text-[10px] font-bold uppercase tracking-[.16em] text-[var(--primary)]"},Fg={class:"mt-2 font-heading text-3xl md:text-4xl font-bold tracking-tight"},Og={class:"mt-2 max-w-2xl text-sm leading-relaxed text-[var(--text-muted)]"},Bg={key:0,class:"grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5"},kg=["src","alt"],zg={key:1,class:"absolute inset-0 grid place-items-center text-5xl text-white/60"},Gg={class:"absolute left-3 top-3 rounded-full bg-[#0a0e18]/80 px-2.5 py-1 font-mono text-[10px] uppercase tracking-wide text-[#ff9a52] backdrop-blur"},Hg={class:"p-4 md:p-5"},Vg={class:"font-mono text-[10px] uppercase tracking-wider text-[var(--primary)]"},Wg={class:"mt-1.5 line-clamp-2 min-h-12 text-base font-bold leading-snug transition group-hover:text-[var(--primary)]"},Xg={class:"border-t border-white/5 bg-[#171b26]/60 px-4 py-3 md:px-5 flex items-center justify-between"},qg={class:"font-heading text-lg font-bold text-[#4cd7f6]"},Yg={id:"library",class:"max-w-7xl mx-auto px-4 md:px-6 py-14 md:py-20 scroll-mt-24 border-t border-white/5"},Kg={class:"mb-8 flex flex-col md:flex-row md:items-end justify-between gap-4"},Zg={class:"font-mono text-[10px] font-bold uppercase tracking-[.16em] text-[#4cd7f6]"},$g={class:"mt-2 font-heading text-3xl md:text-4xl font-bold tracking-tight"},Jg={class:"mt-2 max-w-2xl text-sm leading-relaxed text-[var(--text-muted)]"},Qg={key:0,class:"grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4 md:gap-5"},jg=["src","alt"],ex={key:1,class:"absolute inset-0 grid place-items-center text-5xl text-white/60"},tx={key:2,class:"absolute left-2.5 top-2.5 rounded-full bg-[#0e513d]/90 px-2.5 py-1 font-mono text-[9px] font-bold uppercase tracking-wide text-[#6ee7b7]"},nx={class:"p-3.5 md:p-4"},ix={class:"font-mono text-[9px] uppercase tracking-wider text-[#4cd7f6]"},sx={class:"mt-1.5 line-clamp-2 min-h-10 text-sm font-semibold leading-snug transition group-hover:text-[var(--primary)]"},rx={class:"mt-2 truncate text-[10px] text-[var(--text-muted)]"},ax={key:0,class:"mt-1 text-[10px] text-[var(--text-muted)]"},ox={id:"pricing",class:"max-w-7xl mx-auto px-4 md:px-6 py-14 md:py-20 scroll-mt-24 border-t border-white/5"},lx={class:"mx-auto mb-10 max-w-2xl text-center"},cx={class:"inline-flex items-center gap-2 rounded-full border border-emerald-500/30 bg-emerald-500/10 px-4 py-1.5 text-xs font-bold text-emerald-400 mb-3"},dx={class:"mt-2 font-heading text-3xl md:text-4xl font-bold tracking-tight text-white"},ux={class:"mt-3 text-sm leading-relaxed text-[var(--text-muted)]"},fx={key:0,class:"grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 items-stretch"},hx={class:"flex items-center justify-between gap-2 mb-2"},px={class:"text-base font-bold text-white"},mx={key:0,class:"mt-1 min-h-8 text-xs leading-relaxed text-[var(--text-muted)]"},gx={class:"my-4 flex flex-col"},xx={class:"flex items-baseline gap-1"},_x={class:"text-xs text-[var(--text-muted)]"},vx={class:"text-[10px] font-medium text-emerald-400 mt-1"},Sx={class:"mb-4 grid grid-cols-2 gap-1.5 text-[11px]"},Mx={class:"p-1.5 rounded-lg bg-gray-100 dark:bg-[#0e1320] text-gray-800 dark:text-gray-300"},bx={class:"p-1.5 rounded-lg bg-gray-100 dark:bg-[#0e1320] text-gray-800 dark:text-gray-300"},yx={class:"flex flex-col gap-2 text-xs text-[var(--text-muted)] pt-2 border-t border-white/5"},Ex={class:"leading-tight"},Tx={key:0,id:"blog",class:"max-w-7xl mx-auto px-4 md:px-6 py-14 md:py-20 scroll-mt-24 border-t border-white/5"},wx={class:"mb-8 flex flex-col md:flex-row md:items-end justify-between gap-4"},Ax={class:"font-mono text-[10px] font-bold uppercase tracking-[.16em] text-[#10b981]"},Rx={class:"mt-2 font-heading text-3xl md:text-4xl font-bold tracking-tight"},Cx={class:"mt-2 max-w-2xl text-sm leading-relaxed text-[var(--text-muted)]"},Px={class:"grid grid-cols-1 md:grid-cols-3 gap-5"},Lx=["src","alt"],Dx={key:1,class:"absolute inset-0 grid place-items-center text-5xl text-white/60"},Ix={class:"p-4 md:p-5 flex flex-col flex-1 justify-between"},Nx={class:"text-[10px] font-bold text-[var(--primary)] uppercase tracking-wider"},Ux={class:"mt-1 text-sm font-bold text-white group-hover:text-[var(--primary)] transition line-clamp-2"},Fx={class:"mt-2 text-xs text-gray-400 line-clamp-2 leading-relaxed"},Ox={class:"mt-4 text-[11px] text-gray-400 font-semibold flex items-center gap-1"},qx={__name:"Home",props:{stats:Object,categories:Array,topCourses:Array,popularBooks:Array,latestBlogs:Array,plans:Array,testimonials:Array,seo:Object,featuredJobs:Array,featuredAdmissions:Array},setup(i){const e=us(null),{locale:t}=Rl(),n=Tl(()=>t.value==="en"),s=new Date("2026-11-25T23:59:59+06:00"),r=new Date("2026-11-30T23:59:59+06:00"),a=us({days:"00",hours:"00",minutes:"00",seconds:"00"}),o=us({days:"00",hours:"00",minutes:"00",seconds:"00"});let d=null;function c(){const g=new Date().getTime(),l=Math.max(0,s.getTime()-g);a.value={days:Math.floor(l/(1e3*60*60*24)).toString().padStart(2,"0"),hours:Math.floor(l%(1e3*60*60*24)/(1e3*60*60)).toString().padStart(2,"0"),minutes:Math.floor(l%(1e3*60*60)/(1e3*60)).toString().padStart(2,"0"),seconds:Math.floor(l%(1e3*60)/1e3).toString().padStart(2,"0")};const T=Math.max(0,r.getTime()-g);o.value={days:Math.floor(T/(1e3*60*60*24)).toString().padStart(2,"0"),hours:Math.floor(T%(1e3*60*60*24)/(1e3*60*60)).toString().padStart(2,"0"),minutes:Math.floor(T%(1e3*60*60)/(1e3*60)).toString().padStart(2,"0"),seconds:Math.floor(T%(1e3*60)/1e3).toString().padStart(2,"0")}}Ao(()=>{c(),d=setInterval(c,1e3)}),Ro(()=>{d&&clearInterval(d)});const m=["from-[#0e513d] to-[#0a0e18]","from-[#7c3d13] to-[#171b26]","from-[#123859] to-[#0a0e18]"],x=["from-[#123858] to-[#0a0e18]","from-[#0e513d] to-[#0a0e18]","from-[#5b2f22] to-[#0a0e18]","from-[#40336e] to-[#0a0e18]","from-[#77521c] to-[#0a0e18]"];function f(g){return g?new Date(g).toLocaleDateString(n.value?"en-US":"bn-BD",{year:"numeric",month:"short",day:"numeric"}):n.value?"Soon":"শীঘ্রই"}return(g,l)=>(We(),qe(Xn,null,[Fe(Al,{seo:i.seo},null,8,["seo"]),Fe(wl,null,{default:Oe(()=>{var T,p,u,b;return[_("div",$p,[l[112]||(l[112]=_("div",{class:"pointer-events-none absolute -top-48 left-[20%] -z-10 h-[620px] w-[620px] rounded-full bg-[var(--primary)]/10 blur-[140px]"},null,-1)),l[113]||(l[113]=_("div",{class:"pointer-events-none absolute top-[1120px] -right-48 -z-10 h-[560px] w-[560px] rounded-full bg-[var(--secondary)]/10 blur-[150px]"},null,-1)),l[114]||(l[114]=_("div",{class:"pointer-events-none absolute top-[2900px] -left-40 -z-10 h-[640px] w-[640px] rounded-full bg-[var(--primary)]/10 blur-[160px]"},null,-1)),_("section",Jp,[_("div",Qp,[_("div",jp,[_("div",em,[l[4]||(l[4]=_("span",{class:"relative flex h-2.5 w-2.5"},[_("span",{class:"absolute inline-flex h-full w-full animate-ping rounded-full bg-[#ff7a00] opacity-60"}),_("span",{class:"relative inline-flex h-2.5 w-2.5 rounded-full bg-[#ff7a00]"})],-1)),_("span",tm,V(n.value?"Learning tools for every next step":"আপনার শেখার প্রতিটি পরবর্তী ধাপে"),1),l[5]||(l[5]=_("span",{class:"hidden sm:inline rounded bg-[#0a0e18] px-2 py-0.5 font-mono text-[9px] text-[var(--text-muted)]"},"SSC · HSC · ADMISSION · JOB",-1))]),_("h1",nm,V(n.value?"A clearer way to read, practice, and learn.":"পড়াশোনা, চাকরি ও ভর্তি প্রস্তুতির সহজ মাধ্যম।"),1),_("p",im,V(n.value?"Access digitized textbooks, mentor-led courses, real-time AI companions, job circulars, and academic calculators—all in one place.":"ডিজিটাল পাঠ্যবই, মেন্টর পরিচালিত কোর্স, লক্ষ্যভিত্তিক এআই স্টাডি সহায়তা, প্রতিদিনের চাকরির সার্কুলার ও ক্যালকুলেটর—সবকিছু এক জায়গায়।"),1),_("div",sm,[Fe(Be(ke),{href:"/register",class:"flex-1 sm:flex-initial inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-[#ff7a00] to-[#f97316] px-6 py-3.5 text-sm font-bold text-white shadow-lg shadow-[#f97316]/25 transition hover:shadow-[0_0_32px_rgba(249,115,22,.4)] hover:-translate-y-0.5"},{default:Oe(()=>[Qe(V(n.value?"Get started free":"বিনামূল্যে শুরু করুন")+" ",1),l[6]||(l[6]=_("span",{"aria-hidden":"true"},"→",-1))]),_:1}),Fe(Be(ke),{href:"/job-news",class:"flex-1 sm:flex-initial inline-flex items-center justify-center gap-2 rounded-xl border border-white/10 bg-[#1c1f2a] px-5 py-3.5 text-sm font-semibold text-[var(--text)] transition hover:bg-[#262a35] hover:border-white/20"},{default:Oe(()=>[_("span",null,V(n.value?"Job News":"চাকরির খবর"),1),l[7]||(l[7]=_("span",{class:"material-symbols-outlined !text-sm"},"work",-1))]),_:1}),Fe(Be(ke),{href:"/calculators",class:"inline-flex items-center justify-center gap-1.5 rounded-xl border border-white/10 bg-[#1c1f2a] px-4 py-3.5 text-sm font-semibold text-[var(--text)] transition hover:bg-[#262a35]"},{default:Oe(()=>[_("span",null,V(n.value?"Calculators":"ক্যালকুলেটর"),1),l[8]||(l[8]=_("span",{class:"material-symbols-outlined !text-sm"},"calculate",-1))]),_:1})])]),_("div",rm,[_("div",am,[Fe(Zp),_("div",om,[_("div",lm,[_("div",cm,V(((p=(T=i.stats)==null?void 0:T.students)==null?void 0:p.toLocaleString(n.value?"en-US":"bn-BD"))||(n.value?"12,000+":"১২,০০০+")),1),_("div",dm,V(n.value?"Active Students":"সক্রিয় শিক্ষার্থী"),1)]),_("div",um,[_("div",fm,V(n.value?"20,000+ MCQ":"২০,০০০+ সলভড প্রশ্ন"),1),_("div",hm,V(n.value?"Question Bank":"বিগত বছরের প্রশ্নব্যাংক"),1)])])])])])]),_("section",pm,[_("div",mm,[_("div",gm,[l[9]||(l[9]=_("span",{class:"w-2 h-2 rounded-full bg-cyan-400 animate-pulse"},null,-1)),_("span",null,V(n.value?"HOW SIKHUN EMPOWERS STUDENTS":"আমরা কী করি এবং কীভাবে পড়াশোনা সহজ করি"),1)]),_("h2",xm,V(n.value?"Everything you need to master exams with AI":"পড়াশোনা, বিশ্ববিদ্যালয় ভর্তি ও চাকরির পূর্ণাঙ্গ স্মার্ট প্রস্তুতি"),1),_("p",_m,V(n.value?"Sikhun combines interactive digital textbooks, 20,000+ university past papers, an automated mistake notebook, and live AI tutors into one focused study cockpit.":"শিখুন কেবল সাধারণ কোনো ওয়েবসাইট নয়—এটি আপনার প্রতিদিনের পার্সোনাল AI শিক্ষক। বই পড়া থেকে শুরু করে মডেল টেস্ট দেওয়া ও ভুল সংশোধন, সবকিছু এখন এক প্ল্যাটফর্মে।"),1)]),_("div",vm,[_("div",Sm,[_("div",null,[l[10]||(l[10]=_("div",{class:"w-12 h-12 rounded-2xl bg-cyan-500/15 border border-cyan-500/30 flex items-center justify-center text-2xl text-cyan-400 mb-5 group-hover:scale-110 transition-transform"}," 📖 ",-1)),l[11]||(l[11]=_("span",{class:"text-[10px] font-bold uppercase tracking-wider text-cyan-400 font-mono"},"Pillar 01",-1)),_("h3",Mm,V(n.value?"AI Book Reader & Chat":"AI বুক রিডার ও স্মার্ট চ্যাট"),1),_("p",bm,V(n.value?"Read textbooks and highlight any tricky paragraph. Sikhun AI explains it in simple Bangla with examples.":"পাঠ্যবই পড়ার সময় যেকোনো প্যারাগ্রাফে সরাসরি AI-কে বাংলায় প্রশ্ন করুন। কঠিন সংজ্ঞা, গাণিতিক নিয়ম বা চ্যাপ্টার সামারি চোখের পলকে বুঝে নিন।"),1)]),Fe(Be(ke),{href:"/library",class:"mt-5 inline-flex items-center gap-1.5 text-xs font-bold text-cyan-400 hover:text-cyan-300 group-hover:translate-x-1 transition-transform"},{default:Oe(()=>[_("span",null,V(n.value?"Browse Library":"বই লাইব্রেরি দেখুন"),1),l[12]||(l[12]=_("span",null,"→",-1))]),_:1})]),_("div",ym,[_("div",null,[l[13]||(l[13]=_("div",{class:"w-12 h-12 rounded-2xl bg-amber-500/15 border border-amber-500/30 flex items-center justify-center text-2xl text-amber-400 mb-5 group-hover:scale-110 transition-transform"}," 🎯 ",-1)),l[14]||(l[14]=_("span",{class:"text-[10px] font-bold uppercase tracking-wider text-amber-400 font-mono"},"Pillar 02",-1)),_("h3",Em,V(n.value?"20,000+ Question Bank & Explainer":"বিশাল প্রশ্নব্যাংক ও AI ব্যাখ্যা"),1),_("p",Tm,V(n.value?"Practice 25 years of DU, RU, BUET, GST, Medical & BCS questions with instant negative-mark calculations.":"ঢাবি, রাবি, বুয়েট, মেডিকেল, গুচ্ছ ও বিসিএস-এর বিগত ২৫ বছরের প্রশ্ন। ভুল উত্তর দিলে AI সাথে সাথে বিস্তারিত নিয়ম বুঝিয়ে দেবে।"),1)]),Fe(Be(ke),{href:"/question-bank",class:"mt-5 inline-flex items-center gap-1.5 text-xs font-bold text-amber-400 hover:text-amber-300 group-hover:translate-x-1 transition-transform"},{default:Oe(()=>[_("span",null,V(n.value?"Practice Question Bank":"প্রশ্নব্যাংক সলভ করুন"),1),l[15]||(l[15]=_("span",null,"→",-1))]),_:1})]),_("div",wm,[_("div",null,[l[16]||(l[16]=_("div",{class:"w-12 h-12 rounded-2xl bg-rose-500/15 border border-rose-500/30 flex items-center justify-center text-2xl text-rose-400 mb-5 group-hover:scale-110 transition-transform"}," 📝 ",-1)),l[17]||(l[17]=_("span",{class:"text-[10px] font-bold uppercase tracking-wider text-rose-400 font-mono"},"Pillar 03",-1)),_("h3",Am,V(n.value?"Automated Mistake Notebook":"ভুল উত্তরের স্বয়ংক্রিয় খাতা"),1),_("p",Rm,V(n.value?"Never repeat a mistake. Tests automatically save your incorrect answers into a dedicated revision notebook.":"মডেল টেস্টে আপনি যে যে প্রশ্নে ভুল করবেন, সেগুলো আলাদা খাতায় জমা থাকে। পরীক্ষার আগের রাতে শুধু ভুলের খাতা রিভিশন দিন।"),1)]),Fe(Be(ke),{href:"/mistakes",class:"mt-5 inline-flex items-center gap-1.5 text-xs font-bold text-rose-400 hover:text-rose-300 group-hover:translate-x-1 transition-transform"},{default:Oe(()=>[_("span",null,V(n.value?"Open Mistake Book":"ভুলের খাতা দেখুন"),1),l[18]||(l[18]=_("span",null,"→",-1))]),_:1})]),_("div",Cm,[_("div",null,[l[19]||(l[19]=_("div",{class:"w-12 h-12 rounded-2xl bg-emerald-500/15 border border-emerald-500/30 flex items-center justify-center text-2xl text-emerald-400 mb-5 group-hover:scale-110 transition-transform"}," 🎓 ",-1)),l[20]||(l[20]=_("span",{class:"text-[10px] font-bold uppercase tracking-wider text-emerald-400 font-mono"},"Pillar 04",-1)),_("h3",Pm,V(n.value?"Official Circular Tracker":"ভর্তি ও চাকরির নোটিশ ট্র্যাকার"),1),_("p",Lm,V(n.value?"Direct links to official application portals, deadlines, GPA requirements, and admission eligibility rules.":"সকল পাবলিক, প্রাইভেট ও গুচ্ছ বিশ্ববিদ্যালয়ের অফিসিয়াল সার্কুলার, লাইভ কাউন্টডাউন এবং ৩য় পক্ষের বিভ্রান্তি ছাড়া সরাসরি আবেদন।"),1)]),Fe(Be(ke),{href:"/admission-notice",class:"mt-5 inline-flex items-center gap-1.5 text-xs font-bold text-emerald-400 hover:text-emerald-300 group-hover:translate-x-1 transition-transform"},{default:Oe(()=>[_("span",null,V(n.value?"View Circulars":"ভর্তি সার্কুলারসমূহ"),1),l[21]||(l[21]=_("span",null,"→",-1))]),_:1})])]),_("div",Dm,[l[32]||(l[32]=_("div",{class:"flex flex-col lg:flex-row lg:items-center justify-between gap-6 pb-6 border-b border-white/10"},[_("div",null,[_("div",{class:"inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-cyan-500/20 text-cyan-300 text-xs font-bold mb-2"},[_("span",null,"⚡"),_("span",null,"ইন্টারেক্টিভ ডেমো: নিজে ট্রাই করে দেখুন")]),_("h3",{class:"text-xl sm:text-2xl font-bold text-white font-heading"}," কীভাবে শিখুন AI এক্সপ্লেনার কাজ করে? "),_("p",{class:"text-xs sm:text-sm text-gray-300 mt-1"}," নিচের প্রশ্নটিতে যেকোনো একটি অপশনে ক্লিক করুন এবং দেখুন এআই কীভাবে মুহূর্তেই বিস্তারিত নিয়ম বুঝিয়ে দেয়: ")]),_("div",{class:"flex items-center gap-2"},[_("span",{class:"px-3 py-1.5 rounded-xl bg-orange-500/15 border border-orange-500/30 text-orange-400 text-xs font-bold"}," ঢাবি ও বিসিএস বিগত প্রশ্ন ")])],-1)),_("div",Im,[_("div",Nm,[l[26]||(l[26]=_("div",{class:"p-4 rounded-2xl bg-white/5 border border-white/10"},[_("span",{class:"text-xs font-bold text-cyan-400 uppercase tracking-wider font-mono"},"নমুনা প্রশ্ন"),_("p",{class:"text-base sm:text-lg font-bold text-white mt-1"},' "বাংলা বর্ণমালায় অর্ধমাত্রা বর্ণ কয়টি?" ')],-1)),_("div",Um,[_("button",{type:"button",onClick:l[0]||(l[0]=y=>e.value="A"),class:en(["p-3.5 rounded-xl text-left font-semibold text-sm transition-all border flex items-center justify-between cursor-pointer",e.value==="A"?"bg-rose-500/20 border-rose-500 text-rose-300":"bg-white/5 border-white/10 text-gray-200 hover:bg-white/10 hover:border-white/20"])},[l[22]||(l[22]=_("span",null,"ক. ৭টি",-1)),e.value==="A"?(We(),qe("span",Fm,"ভুল উত্তর ✕")):Vt("",!0)],2),_("button",{type:"button",onClick:l[1]||(l[1]=y=>e.value="B"),class:en(["p-3.5 rounded-xl text-left font-semibold text-sm transition-all border flex items-center justify-between cursor-pointer",e.value==="B"?"bg-emerald-500/25 border-emerald-500 text-emerald-300 shadow-lg shadow-emerald-500/20":"bg-white/5 border-white/10 text-gray-200 hover:bg-white/10 hover:border-white/20"])},[l[23]||(l[23]=_("span",null,"খ. ৮টি",-1)),e.value==="B"?(We(),qe("span",Om,"সঠিক উত্তর ✓")):e.value&&e.value!=="B"?(We(),qe("span",Bm,"সঠিক উত্তর ✓")):Vt("",!0)],2),_("button",{type:"button",onClick:l[2]||(l[2]=y=>e.value="C"),class:en(["p-3.5 rounded-xl text-left font-semibold text-sm transition-all border flex items-center justify-between cursor-pointer",e.value==="C"?"bg-rose-500/20 border-rose-500 text-rose-300":"bg-white/5 border-white/10 text-gray-200 hover:bg-white/10 hover:border-white/20"])},[l[24]||(l[24]=_("span",null,"গ. ১০টি",-1)),e.value==="C"?(We(),qe("span",km,"ভুল উত্তর ✕")):Vt("",!0)],2),_("button",{type:"button",onClick:l[3]||(l[3]=y=>e.value="D"),class:en(["p-3.5 rounded-xl text-left font-semibold text-sm transition-all border flex items-center justify-between cursor-pointer",e.value==="D"?"bg-rose-500/20 border-rose-500 text-rose-300":"bg-white/5 border-white/10 text-gray-200 hover:bg-white/10 hover:border-white/20"])},[l[25]||(l[25]=_("span",null,"ঘ. ১১টি",-1)),e.value==="D"?(We(),qe("span",zm,"ভুল উত্তর ✕")):Vt("",!0)],2)])]),_("div",Gm,[_("div",Hm,[_("div",null,[l[29]||(l[29]=_("div",{class:"flex items-center gap-2 text-indigo-400 font-bold text-xs uppercase tracking-wider mb-2"},[_("span",{class:"w-2 h-2 rounded-full bg-indigo-400 animate-ping"}),_("span",null,"✨ Sikhun AI Explainer")],-1)),e.value?(We(),qe("div",Vm,[...l[27]||(l[27]=[_("p",{class:"font-bold text-emerald-400"}," সঠিক উত্তর: (খ) ৮টি ",-1),_("p",null,[Qe(" বাংলা বর্ণমালায় অর্ধমাত্রা বর্ণ মোট "),_("strong",null,"৮টি"),Qe("। এর মধ্যে স্বরবর্ণে ১টি: "),_("em",null,"ঋ"),Qe(", এবং ব্যঞ্জনবর্ণে ৭টি: "),_("em",null,"খ, গ, ণ, থ, ধ, প, শ"),Qe("। ")],-1),_("p",{class:"text-[11px] text-gray-400 pt-1 border-t border-indigo-400/20"}," 💡 মনে রাখার টিপস: পূর্ণমাত্রা ৩২টি, অর্ধমাত্রা ৮টি এবং মাত্রাহীন বর্ণ ১০টি (মোট ৫০টি)। ",-1)])])):(We(),qe("div",Wm,[...l[28]||(l[28]=[_("span",{class:"text-2xl block mb-2"},"👆",-1),Qe(" বামের যেকোনো একটি অপশনে ক্লিক করলেই AI Explainer এর রিয়েল-টাইম ব্যাখ্যা চালু হবে। ",-1)])]))]),_("div",Xm,[l[31]||(l[31]=_("span",{class:"text-[11px] text-gray-400"},"সকল প্রশ্নে এমন ব্যাখ্যা চান?",-1)),Fe(Be(ke),{href:"/question-bank",class:"text-xs font-bold text-cyan-400 hover:underline"},{default:Oe(()=>[...l[30]||(l[30]=[Qe(" প্রশ্নব্যাংকে যান → ",-1)])]),_:1})])])])])])]),_("section",qm,[_("div",Ym,[l[72]||(l[72]=_("div",{class:"pointer-events-none absolute -top-24 right-10 w-96 h-96 bg-orange-500/15 rounded-full blur-3xl"},null,-1)),l[73]||(l[73]=_("div",{class:"pointer-events-none absolute -bottom-24 left-10 w-96 h-96 bg-blue-500/15 rounded-full blur-3xl"},null,-1)),_("div",Km,[_("div",Zm,[_("div",null,[_("div",$m,[l[33]||(l[33]=_("span",{class:"relative flex h-2 w-2"},[_("span",{class:"animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"}),_("span",{class:"relative inline-flex rounded-full h-2 w-2 bg-rose-500"})],-1)),_("span",null,V(n.value?"ADMISSIONS 2026-2027":"ভর্তি সেশন ২০২৬-২০২৭ লাইভ"),1)]),_("h2",Jm,V(n.value?"University Admission Preparation 2026-2027":"বিশ্ববিদ্যালয় ভর্তি প্রস্তুতি ২০২৬-২০২৭"),1),_("p",Qm,V(n.value?"Live application deadlines, 15,000+ past year solved questions for DU, RU, BUET & GST with realistic model tests and instant AI explanations.":"ঢাকা, রাজশাহী, বুয়েট ও গুচ্ছসহ সকল বিশ্ববিদ্যালয়ের আবেদনের সময়সীমা, বিগত ২৫ বছরের ১৫,০০০+ সলভড প্রশ্নব্যাংক এবং এআই সমাধানসহ ফ্রি মডেল টেস্ট।"),1)]),_("div",jm,[Fe(Be(ke),{href:"/question-bank?segment=admission",class:"px-5 py-3 rounded-xl bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white font-bold text-xs sm:text-sm shadow-lg shadow-orange-500/25 transition flex items-center gap-2 hover:-translate-y-0.5"},{default:Oe(()=>[l[34]||(l[34]=_("span",{class:"material-symbols-outlined !text-base"},"menu_book",-1)),_("span",null,V(n.value?"Admission Question Bank":"ভর্তি প্রশ্নব্যাংক"),1),l[35]||(l[35]=_("span",null,"→",-1))]),_:1}),Fe(Be(ke),{href:"/question-bank?segment=admission",class:"px-5 py-3 rounded-xl bg-blue-600/90 hover:bg-blue-600 text-white font-bold text-xs sm:text-sm border border-blue-400/30 shadow-lg shadow-blue-500/20 transition flex items-center gap-2 hover:-translate-y-0.5"},{default:Oe(()=>[l[36]||(l[36]=_("span",{class:"material-symbols-outlined !text-base"},"quiz",-1)),_("span",null,V(n.value?"Free Model Test":"ফ্রি মডেল টেস্ট"),1),l[37]||(l[37]=_("span",{class:"px-1.5 py-0.2 text-[10px] rounded bg-white/20 text-white font-bold"},"১০টি প্রশ্ন",-1))]),_:1}),Fe(Be(ke),{href:"/admission-notice",class:"px-4 py-3 rounded-xl border border-white/15 bg-white/5 hover:bg-white/10 text-gray-200 font-semibold text-xs sm:text-sm transition flex items-center gap-1.5"},{default:Oe(()=>[l[38]||(l[38]=_("span",{class:"material-symbols-outlined !text-base"},"campaign",-1)),_("span",null,V(n.value?"All Admission Circulars":"সকল ভর্তি সার্কুলার"),1)]),_:1})])]),_("div",e0,[_("div",t0,[l[53]||(l[53]=_("div",{class:"flex items-start justify-between gap-3 mb-4"},[_("div",null,[_("span",{class:"px-2.5 py-1 rounded-full bg-blue-500/20 text-blue-300 text-[11px] font-bold uppercase tracking-wider inline-block mb-1.5"}," ঢাকা বিশ্ববিদ্যালয় (DU) "),_("h3",{class:"text-lg sm:text-xl font-bold font-heading text-white"}," আন্ডারগ্র্যাজুয়েট ভর্তি পরীক্ষা ২০২৬ "),_("p",{class:"text-xs text-blue-200/80 mt-0.5"},[Qe(" আবেদনের শেষ সময়: "),_("strong",{class:"text-rose-400 font-bold"},"২৫ নভেম্বর ২০২৬")])]),_("span",{class:"px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-rose-500/20 text-rose-300 border border-rose-500/30 animate-pulse"}," আবেদন চলছে ")],-1)),_("div",n0,[_("div",null,[_("div",i0,V(a.value.days),1),l[39]||(l[39]=_("div",{class:"text-[10px] text-blue-300 uppercase font-sans"},"দিন",-1))]),l[43]||(l[43]=_("span",{class:"text-blue-500 font-bold text-lg"},":",-1)),_("div",null,[_("div",s0,V(a.value.hours),1),l[40]||(l[40]=_("div",{class:"text-[10px] text-blue-300 uppercase font-sans"},"ঘণ্টা",-1))]),l[44]||(l[44]=_("span",{class:"text-blue-500 font-bold text-lg"},":",-1)),_("div",null,[_("div",r0,V(a.value.minutes),1),l[41]||(l[41]=_("div",{class:"text-[10px] text-blue-300 uppercase font-sans"},"মিনিট",-1))]),l[45]||(l[45]=_("span",{class:"text-blue-500 font-bold text-lg"},":",-1)),_("div",null,[_("div",a0,V(a.value.seconds),1),l[42]||(l[42]=_("div",{class:"text-[10px] text-blue-300 uppercase font-sans"},"সেকেন্ড",-1))])]),_("div",o0,[l[50]||(l[50]=_("div",{class:"text-[11px] font-semibold text-gray-400 uppercase tracking-wider"}," ইউনিটভিত্তিক প্রশ্নব্যাংক অনুশীলন: ",-1)),_("div",l0,[Fe(Be(ke),{href:"/question-bank?segment=admission&origin=DU-A",class:"p-2 rounded-xl bg-white/5 hover:bg-orange-500/20 border border-white/5 hover:border-orange-500/40 text-center transition group/u"},{default:Oe(()=>[...l[46]||(l[46]=[_("div",{class:"text-xs font-bold text-white group-hover/u:text-orange-400"},"ক ইউনিট",-1),_("div",{class:"text-[10px] text-gray-400"},"১৬৯৩ প্রশ্ন",-1)])]),_:1}),Fe(Be(ke),{href:"/question-bank?segment=admission&origin=DU-B",class:"p-2 rounded-xl bg-white/5 hover:bg-orange-500/20 border border-white/5 hover:border-orange-500/40 text-center transition group/u"},{default:Oe(()=>[...l[47]||(l[47]=[_("div",{class:"text-xs font-bold text-white group-hover/u:text-orange-400"},"খ ইউনিট",-1),_("div",{class:"text-[10px] text-gray-400"},"৮৪০ প্রশ্ন",-1)])]),_:1}),Fe(Be(ke),{href:"/question-bank?segment=admission&origin=DU-C",class:"p-2 rounded-xl bg-white/5 hover:bg-orange-500/20 border border-white/5 hover:border-orange-500/40 text-center transition group/u"},{default:Oe(()=>[...l[48]||(l[48]=[_("div",{class:"text-xs font-bold text-white group-hover/u:text-orange-400"},"গ ইউনিট",-1),_("div",{class:"text-[10px] text-gray-400"},"১৩৯৫ প্রশ্ন",-1)])]),_:1}),Fe(Be(ke),{href:"/question-bank?segment=admission&origin=DU-D",class:"p-2 rounded-xl bg-white/5 hover:bg-orange-500/20 border border-white/5 hover:border-orange-500/40 text-center transition group/u"},{default:Oe(()=>[...l[49]||(l[49]=[_("div",{class:"text-xs font-bold text-white group-hover/u:text-orange-400"},"ঘ / IBA",-1),_("div",{class:"text-[10px] text-gray-400"},"৮৩৭ প্রশ্ন",-1)])]),_:1})])]),_("div",c0,[Fe(Be(ke),{href:"/admission-notice",class:"text-blue-300 hover:text-white font-medium flex items-center gap-1"},{default:Oe(()=>[...l[51]||(l[51]=[_("span",null,"অফিসিয়াল সার্কুলার দেখুন",-1),_("span",null,"→",-1)])]),_:1}),Fe(Be(ke),{href:"/question-bank?segment=admission&origin=DU-A",class:"text-orange-400 hover:text-orange-300 font-bold flex items-center gap-1"},{default:Oe(()=>[...l[52]||(l[52]=[_("span",null,"ঢাবি সম্পূর্ণ প্রশ্নব্যাংক",-1),_("span",{class:"material-symbols-outlined !text-xs"},"north_east",-1)])]),_:1})])]),_("div",d0,[l[67]||(l[67]=_("div",{class:"flex items-start justify-between gap-3 mb-4"},[_("div",null,[_("span",{class:"px-2.5 py-1 rounded-full bg-purple-500/20 text-purple-300 text-[11px] font-bold uppercase tracking-wider inline-block mb-1.5"}," রাজশাহী বিশ্ববিদ্যালয় (RU) "),_("h3",{class:"text-lg sm:text-xl font-bold font-heading text-white"}," স্নাতক প্রথম বর্ষ ভর্তি পরীক্ষা ২০২৬ "),_("p",{class:"text-xs text-purple-200/80 mt-0.5"},[Qe(" আবেদনের শেষ সময়: "),_("strong",{class:"text-rose-400 font-bold"},"৩০ নভেম্বর ২০২৬")])]),_("span",{class:"px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-rose-500/20 text-rose-300 border border-rose-500/30 animate-pulse"}," আবেদন চলছে ")],-1)),_("div",u0,[_("div",null,[_("div",f0,V(o.value.days),1),l[54]||(l[54]=_("div",{class:"text-[10px] text-purple-300 uppercase font-sans"},"দিন",-1))]),l[58]||(l[58]=_("span",{class:"text-purple-500 font-bold text-lg"},":",-1)),_("div",null,[_("div",h0,V(o.value.hours),1),l[55]||(l[55]=_("div",{class:"text-[10px] text-purple-300 uppercase font-sans"},"ঘণ্টা",-1))]),l[59]||(l[59]=_("span",{class:"text-purple-500 font-bold text-lg"},":",-1)),_("div",null,[_("div",p0,V(o.value.minutes),1),l[56]||(l[56]=_("div",{class:"text-[10px] text-purple-300 uppercase font-sans"},"মিনিট",-1))]),l[60]||(l[60]=_("span",{class:"text-purple-500 font-bold text-lg"},":",-1)),_("div",null,[_("div",m0,V(o.value.seconds),1),l[57]||(l[57]=_("div",{class:"text-[10px] text-purple-300 uppercase font-sans"},"সেকেন্ড",-1))])]),_("div",g0,[l[64]||(l[64]=_("div",{class:"text-[11px] font-semibold text-gray-400 uppercase tracking-wider"}," ইউনিটভিত্তিক প্রশ্নব্যাংক অনুশীলন: ",-1)),_("div",x0,[Fe(Be(ke),{href:"/question-bank?segment=admission&origin=RU-A",class:"p-2 rounded-xl bg-white/5 hover:bg-orange-500/20 border border-white/5 hover:border-orange-500/40 text-center transition group/u"},{default:Oe(()=>[...l[61]||(l[61]=[_("div",{class:"text-xs font-bold text-white group-hover/u:text-orange-400"},"A ইউনিট (মানবিক)",-1),_("div",{class:"text-[10px] text-gray-400"},"২৫২৯ প্রশ্ন",-1)])]),_:1}),Fe(Be(ke),{href:"/question-bank?segment=admission&origin=RU-B",class:"p-2 rounded-xl bg-white/5 hover:bg-orange-500/20 border border-white/5 hover:border-orange-500/40 text-center transition group/u"},{default:Oe(()=>[...l[62]||(l[62]=[_("div",{class:"text-xs font-bold text-white group-hover/u:text-orange-400"},"B ইউনিট (বাণিজ্য)",-1),_("div",{class:"text-[10px] text-gray-400"},"৬৯৬ প্রশ্ন",-1)])]),_:1}),Fe(Be(ke),{href:"/question-bank?segment=admission&origin=RU-C",class:"p-2 rounded-xl bg-white/5 hover:bg-orange-500/20 border border-white/5 hover:border-orange-500/40 text-center transition group/u"},{default:Oe(()=>[...l[63]||(l[63]=[_("div",{class:"text-xs font-bold text-white group-hover/u:text-orange-400"},"C ইউনিট (বিজ্ঞান)",-1),_("div",{class:"text-[10px] text-gray-400"},"১৯৩১ প্রশ্ন",-1)])]),_:1})])]),_("div",_0,[Fe(Be(ke),{href:"/admission-notice",class:"text-purple-300 hover:text-white font-medium flex items-center gap-1"},{default:Oe(()=>[...l[65]||(l[65]=[_("span",null,"অফিসিয়াল সার্কুলার দেখুন",-1),_("span",null,"→",-1)])]),_:1}),Fe(Be(ke),{href:"/question-bank?segment=admission&origin=RU-A",class:"text-orange-400 hover:text-orange-300 font-bold flex items-center gap-1"},{default:Oe(()=>[...l[66]||(l[66]=[_("span",null,"রাবি সম্পূর্ণ প্রশ্নব্যাংক",-1),_("span",{class:"material-symbols-outlined !text-xs"},"north_east",-1)])]),_:1})])])]),_("div",v0,[_("span",S0,V(n.value?"More University Question Banks:":"অন্যান্য বিশ্ববিদ্যালয় প্রশ্নব্যাংক:"),1),_("div",M0,[Fe(Be(ke),{href:"/question-bank?segment=admission&origin=BUET",class:"px-3 py-1.5 rounded-lg bg-white/5 hover:bg-white/15 border border-white/10 text-white font-medium transition flex items-center gap-1"},{default:Oe(()=>[...l[68]||(l[68]=[_("span",null,"বুয়েট ও ইঞ্জিনিয়ারিং",-1),_("span",{class:"text-[10px] text-orange-400"},"১৭০০+",-1)])]),_:1}),Fe(Be(ke),{href:"/question-bank?segment=admission&origin=GST-C",class:"px-3 py-1.5 rounded-lg bg-white/5 hover:bg-white/15 border border-white/10 text-white font-medium transition flex items-center gap-1"},{default:Oe(()=>[...l[69]||(l[69]=[_("span",null,"জিএসটি গুচ্ছ (২৪ ভার্সিটি)",-1),_("span",{class:"text-[10px] text-orange-400"},"১৬০০+",-1)])]),_:1}),Fe(Be(ke),{href:"/question-bank?segment=admission&search=মেডিকেল",class:"px-3 py-1.5 rounded-lg bg-white/5 hover:bg-white/15 border border-white/10 text-white font-medium transition flex items-center gap-1"},{default:Oe(()=>[...l[70]||(l[70]=[_("span",null,"মেডিকেল ও ডেন্টাল",-1),_("span",{class:"text-[10px] text-orange-400"},"১২০০+",-1)])]),_:1}),Fe(Be(ke),{href:"/admission-notice",class:"px-3 py-1.5 rounded-lg bg-orange-500/20 hover:bg-orange-500/30 border border-orange-500/30 text-orange-300 font-bold transition flex items-center gap-1"},{default:Oe(()=>[...l[71]||(l[71]=[_("span",null,"সকল সার্কুলার দেখুন →",-1)])]),_:1})])])])])]),_("section",b0,[_("div",y0,[_("div",null,[_("span",E0,V(n.value?"FREE STUDENT & JOB UTILITIES":"ফ্রি স্টুডেন্ট ও জব ইউটিলিটিস"),1),_("h2",T0,V(n.value?"Student Tools & Calculators":"স্টুডেন্ট টুলস ও ক্যালকুলেটর"),1),_("p",w0,V(n.value?"Free government job age calculator, SSC/HSC GPA calculation, and board exam result checker without login.":"কোনো রেজিস্ট্রেশন বা লগইন ছাড়াই ব্যবহার করুন সরকারি চাকরির বয়স ক্যালকুলেটর, জিপিএ ও রেজাল্ট চেকার।"),1)]),_("div",A0,[Fe(Be(ke),{href:"/calculators",class:"inline-flex items-center gap-1.5 text-sm font-semibold text-amber-500 dark:text-amber-400 hover:text-[var(--primary)] transition"},{default:Oe(()=>[_("span",null,V(n.value?"All Calculators & Tools":"সকল ক্যালকুলেটর ও টুলস"),1),l[74]||(l[74]=Qe()),l[75]||(l[75]=_("span",{class:"material-symbols-outlined !text-sm"},"arrow_forward",-1))]),_:1})])]),_("div",R0,[Fe(Be(ke),{href:"/calculators",class:"group p-6 rounded-3xl border border-gray-200 dark:border-white/5 bg-white dark:bg-gradient-to-br dark:from-[#1a202f] dark:to-[#141824] hover:border-amber-500/40 transition-all duration-300 hover:-translate-y-1 shadow-sm dark:shadow-lg"},{default:Oe(()=>[l[78]||(l[78]=_("div",{class:"w-12 h-12 rounded-2xl bg-amber-500/15 text-amber-500 dark:text-amber-400 flex items-center justify-center text-2xl mb-4 group-hover:scale-110 transition-transform"},[_("span",{class:"material-symbols-outlined"},"badge")],-1)),_("h3",C0,V(n.value?"Govt Job Age Calculator":"সরকারি চাকরির বয়স ক্যালকুলেটর"),1),_("p",P0,V(n.value?"Calculate exact age in years, months, and days with general (30 yrs) and quota (32 yrs) eligibility checks.":"জন্মতারিখ ও সার্কুলার দিয়ে সাধারণ (৩০ বছর) ও কোটা (৩২ বছর) অনুযায়ী বছর, মাস ও দিনে নির্ভুল বয়স যাচাই করুন।"),1),_("span",L0,[_("span",null,V(n.value?"Calculate Age":"বয়স হিসাব করুন"),1),l[76]||(l[76]=Qe()),l[77]||(l[77]=_("span",null,"→",-1))])]),_:1}),Fe(Be(ke),{href:"/calculators",class:"group p-6 rounded-3xl border border-gray-200 dark:border-white/5 bg-white dark:bg-gradient-to-br dark:from-[#1a202f] dark:to-[#141824] hover:border-blue-500/40 transition-all duration-300 hover:-translate-y-1 shadow-sm dark:shadow-lg"},{default:Oe(()=>[l[81]||(l[81]=_("div",{class:"w-12 h-12 rounded-2xl bg-blue-500/15 text-blue-500 dark:text-blue-400 flex items-center justify-center text-2xl mb-4 group-hover:scale-110 transition-transform"},[_("span",{class:"material-symbols-outlined"},"calculate")],-1)),_("h3",D0,V(n.value?"SSC / HSC GPA & NU CGPA":"SSC / HSC GPA ও NU CGPA"),1),_("p",I0,V(n.value?"Calculate secondary and higher secondary GPA with 4th subject bonus points, plus National University honours CGPA.":"শিক্ষাবোর্ডের নিয়মানুযায়ী ৪র্থ বিষয়ের বোনাস পয়েন্টসহ জিপিএ এবং জাতীয় বিশ্ববিদ্যালয়ের অনার্স সিজিপিএ বের করুন।"),1),_("span",N0,[_("span",null,V(n.value?"Calculate GPA":"জিপিএ হিসাব করুন"),1),l[79]||(l[79]=Qe()),l[80]||(l[80]=_("span",null,"→",-1))])]),_:1}),Fe(Be(ke),{href:"/admission-notice",class:"group p-6 rounded-3xl border border-gray-200 dark:border-white/5 bg-white dark:bg-gradient-to-br dark:from-[#1a202f] dark:to-[#141824] hover:border-emerald-500/40 transition-all duration-300 hover:-translate-y-1 shadow-sm dark:shadow-lg"},{default:Oe(()=>[l[84]||(l[84]=_("div",{class:"w-12 h-12 rounded-2xl bg-emerald-500/15 text-emerald-500 dark:text-emerald-400 flex items-center justify-center text-2xl mb-4 group-hover:scale-110 transition-transform"},[_("span",{class:"material-symbols-outlined"},"account_balance")],-1)),_("h3",U0,V(n.value?"University Admission Notices":"বিশ্ববিদ্যালয় ভর্তি বিজ্ঞপ্তি"),1),_("p",F0,V(n.value?"Public, Guccho, and Private university admission circulars, eligibility, units, and deadlines.":"পাবলিক, গুচ্ছ ও শীর্ষ বেসরকারি বিশ্ববিদ্যালয়ের ভর্তি বিজ্ঞপ্তি, যোগ্যতা, ইউনিট ও সময়সূচির সম্পূর্ণ গাইড।"),1),_("span",O0,[_("span",null,V(n.value?"View Notices":"ভর্তি বিজ্ঞপ্তি দেখুন"),1),l[82]||(l[82]=Qe()),l[83]||(l[83]=_("span",null,"→",-1))])]),_:1})])]),_("section",B0,[_("div",k0,[_("div",null,[_("span",z0,V(n.value?"CAREERS & RECRUITMENT":"ক্যারিয়ার ও নিয়োগ বিজ্ঞপ্তি"),1),_("h2",G0,V(n.value?"Latest Job News":"সর্বশেষ চাকরির খবর ও সার্কুলার"),1),_("p",H0,V(n.value?"Verified circulars from BCS, Bangladesh Bank, Government departments, and leading private organizations.":"বিসিএস, বাংলাদেশ ব্যাংক, সরকারি বিভিন্ন দপ্তর ও শীর্ষ বেসরকারি প্রতিষ্ঠানের সাম্প্রতিক নিয়োগ বিজ্ঞপ্তি।"),1)]),Fe(Be(ke),{href:"/job-news",class:"inline-flex items-center gap-1.5 text-sm font-semibold text-emerald-500 dark:text-emerald-400 hover:text-[var(--primary)] transition"},{default:Oe(()=>[_("span",null,V(n.value?"All Job News":"সকল চাকরির খবর"),1),l[85]||(l[85]=Qe()),l[86]||(l[86]=_("span",{class:"material-symbols-outlined !text-sm"},"arrow_forward",-1))]),_:1})]),(u=i.featuredJobs)!=null&&u.length?(We(),qe("div",V0,[(We(!0),qe(Xn,null,ti(i.featuredJobs,y=>(We(),qe("article",{key:y.id,class:"p-5 rounded-3xl border border-gray-200 dark:border-white/5 bg-white dark:bg-[#171c28] hover:border-[var(--primary)]/40 transition-all duration-300 hover:-translate-y-1 flex flex-col justify-between shadow-sm dark:shadow-none"},[_("div",null,[_("div",W0,[_("span",X0,V(y.category),1),_("span",q0,V(n.value?"Ends:":"শেষ:")+" "+V(f(y.deadline)),1)]),_("span",Y0,V(y.organization),1),Fe(Be(ke),{href:`/job-news/${y.slug}`},{default:Oe(()=>[_("h3",K0,V(y.title),1)]),_:2},1032,["href"]),_("div",Z0,[y.vacancy?(We(),qe("div",$0,[Qe(V(n.value?"Vacancy:":"পদ:")+" ",1),_("strong",null,V(y.vacancy),1)])):Vt("",!0),y.salary?(We(),qe("div",J0,V(n.value?"Salary:":"বেতন:")+" "+V(y.salary),1)):Vt("",!0)])]),_("div",Q0,[Fe(Be(ke),{href:`/job-news/${y.slug}`,class:"font-bold text-gray-900 dark:text-white hover:text-[var(--primary)]"},{default:Oe(()=>[Qe(V(n.value?"Details →":"বিস্তারিত →"),1)]),_:1},8,["href"]),y.apply_url?(We(),qe("a",{key:0,href:y.apply_url,target:"_blank",class:"px-2.5 py-1 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-[11px]"},V(n.value?"Apply ↗":"আবেদন ↗"),9,j0)):Vt("",!0)])]))),128))])):(We(),qe("div",eg,[Fe(Be(ke),{href:"/job-news",class:"text-[var(--primary)] font-bold"},{default:Oe(()=>[Qe(V(n.value?"Visit job circulars page to view all circulars →":"চাকরির পেজে গিয়ে সকল সার্কুলার দেখুন →"),1)]),_:1})]))]),_("section",tg,[_("div",ng,[_("span",ig,V(n.value?"SPECIAL PREPARATION BUNDLES":"লক্ষ্যভিত্তিক স্পেশাল বান্ডেল"),1),_("h2",sg,V(n.value?"Goal-Oriented Preparation Bundles":"লক্ষ্যভিত্তিক স্পেশাল প্রিপারেশন বান্ডেল"),1),_("p",rg,V(n.value?"Complete 3-month and 6-month specialized preparation packages for university admission, BCS, and bank exams.":"এইচএসসি পরবর্তী ভর্তি পরীক্ষা এবং বিসিএস ও ব্যাংক জব প্রস্তুতির জন্য ৩ মাস বা ৬ মাসের কমপ্লিট প্যাকেজ।"),1)]),_("div",ag,[_("div",og,[l[92]||(l[92]=_("div",{class:"pointer-events-none absolute -right-20 -top-20 h-64 w-64 rounded-full bg-amber-500/10 blur-[80px]"},null,-1)),_("div",null,[_("div",lg,[_("span",cg,V(n.value?"Admission Special (3 & 6 Months)":"ভর্তি পরীক্ষা স্পেশাল (৩ ও ৬ মাস)"),1),l[87]||(l[87]=_("span",{class:"text-xs font-bold text-gray-700 dark:text-white bg-gray-100 dark:bg-white/10 px-3 py-1 rounded-full"},"BUET · Medical · DU · GST",-1))]),_("h3",dg,V(n.value?"University Admission Bundle":"অ্যাডমিশন প্রিপারেশন বান্ডেল"),1),_("p",ug,V(n.value?"Continuous top-tier AI reasoning and problem breakdown for tricky equations in engineering and medical entrance tests.":"শীর্ষ প্রকৌশল ও মেডিকেল ভর্তি পরীক্ষার কঠিন সমীকরণ এবং ট্রিকি প্রশ্ন সমাধানে টপ-টায়ার হাই-রিজনিং AI মডেলের সার্বক্ষণিক সহায়তা।"),1),_("div",fg,[_("div",hg,[l[88]||(l[88]=_("span",{class:"material-symbols-outlined !text-base text-amber-500 dark:text-amber-400 mt-0.5"},"check_circle",-1)),_("span",null,[_("strong",null,V(n.value?"25-Year Solved Archive:":"বিগত ২৫ বছরের প্রশ্নব্যাংক:"),1),Qe(" "+V(n.value?"Solved questions from BUET, DU A-Unit, and Medical.":"বুয়েট, ঢাবি ‘ক’ ইউনিট ও মেডিকেল ভর্তি পরীক্ষার সমাধান।"),1)])]),_("div",pg,[l[89]||(l[89]=_("span",{class:"material-symbols-outlined !text-base text-amber-500 dark:text-amber-400 mt-0.5"},"check_circle",-1)),_("span",null,[_("strong",null,V(n.value?"High-Reasoning AI Model:":"টপ-টায়ার হাই-রিজনিং AI মডেল:"),1),Qe(" "+V(n.value?"Step-by-step reasoning for tough physics and advanced math equations.":"গাণিতিক ও পদার্থবিজ্ঞানের জটিল স্টেপ-বাই-স্টেপ ব্যাখ্যা।"),1)])]),_("div",mg,[l[90]||(l[90]=_("span",{class:"material-symbols-outlined !text-base text-amber-500 dark:text-amber-400 mt-0.5"},"check_circle",-1)),_("span",null,[_("strong",null,V(n.value?"Unlimited Model Tests:":"আনলিমিটেড পূর্ণাঙ্গ মডেল টেস্ট:"),1),Qe(" "+V(n.value?"Realistic exam experience with negative marking and timer.":"নেগেটিভ মার্কিংসহ বাস্তবসম্মত ভর্তি পরীক্ষার অভিজ্ঞতা।"),1)])]),_("div",gg,[l[91]||(l[91]=_("span",{class:"material-symbols-outlined !text-base text-amber-500 dark:text-amber-400 mt-0.5"},"check_circle",-1)),_("span",null,[_("strong",null,V(n.value?"Concept Mastery Tracker:":"চ্যাপ্টারভিত্তিক কনসেপ্ট ট্র্যাকার:"),1),Qe(" "+V(n.value?"AI recommendations detecting and fixing weak chapters.":"দুর্বল অধ্যায় চিহ্নিত করে এআই রিকমেন্ডেশন।"),1)])])])]),_("div",xg,[_("div",null,[_("span",_g,V(n.value?"10% OFF on 3 months & 20% MEGA OFF on 6 months!":"৩ মাসে ১০% এবং ৬ মাসে ২০% মেগা ছাড়!"),1),_("span",vg,V(n.value?"Included in Pro and Elite Plans":"প্রো ও এলিট প্যাকেজে অন্তর্ভুক্ত"),1)]),Fe(Be(ke),{href:"/plans",class:"w-full sm:w-auto px-6 py-3.5 rounded-2xl bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-400 hover:to-orange-400 text-white text-xs font-bold shadow-lg shadow-orange-500/20 text-center"},{default:Oe(()=>[Qe(V(n.value?"Start Admission Bundle →":"অ্যাডমিশন বান্ডেল শুরু করুন →"),1)]),_:1})])]),_("div",Sg,[l[98]||(l[98]=_("div",{class:"pointer-events-none absolute -right-20 -top-20 h-64 w-64 rounded-full bg-emerald-500/10 blur-[80px]"},null,-1)),_("div",null,[_("div",Mg,[_("span",bg,V(n.value?"Job Exam Special (3 & 6 Months)":"চাকরি পরীক্ষা স্পেশাল (৩ ও ৬ মাস)"),1),l[93]||(l[93]=_("span",{class:"text-xs font-bold text-gray-700 dark:text-white bg-gray-100 dark:bg-white/10 px-3 py-1 rounded-full"},"BCS · Bank · Primary · Govt",-1))]),_("h3",yg,V(n.value?"Job & BCS Preparation Bundle":"জব ও বিসিএস প্রিপারেশন বান্ডেল"),1),_("p",Eg,V(n.value?"Targeted question bank and daily practice for BCS preliminary, written, state bank, and primary teacher recruitment.":"বিসিএস প্রিলিমিনারি, রিটেন, রাষ্ট্রায়ত্ত ব্যাংক ও প্রাথমিক সহকারী শিক্ষক নিয়োগ পরীক্ষার প্রশ্নব্যাংক ও স্পেশাল ডেইলি প্র্যাকটিস।"),1),_("div",Tg,[_("div",wg,[l[94]||(l[94]=_("span",{class:"material-symbols-outlined !text-base text-emerald-500 dark:text-emerald-400 mt-0.5"},"check_circle",-1)),_("span",null,[_("strong",null,V(n.value?"20-Year BCS & Bank Archives:":"বিগত ২০ বছরের বিসিএস ও ব্যাংক জব প্রশ্নব্যাংক:"),1),Qe(" "+V(n.value?"Subject-wise solved questions with detailed explanations.":"ব্যাখ্যাসহ বিষয়ভিত্তিক সমাধান।"),1)])]),_("div",Ag,[l[95]||(l[95]=_("span",{class:"material-symbols-outlined !text-base text-emerald-500 dark:text-emerald-400 mt-0.5"},"check_circle",-1)),_("span",null,[_("strong",null,V(n.value?"Written Paper AI Feedback:":"লিখিত সৃজনশীল খাতার AI মূল্যায়ন:"),1),Qe(" "+V(n.value?"Constructive feedback on English essays, translations, and composition.":"ইংরেজি রচনা, অনুবাদ ও বাংলা সাহিত্যের গঠনমূলক ফিডব্যাক।"),1)])]),_("div",Rg,[l[96]||(l[96]=_("span",{class:"material-symbols-outlined !text-base text-emerald-500 dark:text-emerald-400 mt-0.5"},"check_circle",-1)),_("span",null,[_("strong",null,V(n.value?"Job Alerts & Age Calculator:":"জব সার্কুলার এলার্ট ও বয়স ক্যালকুলেটর:"),1),Qe(" "+V(n.value?"Track deadlines, age eligibility, and application guidelines.":"আবেদনের শেষ তারিখ ও বয়সসীমা ট্র্যাকিং।"),1)])]),_("div",Cg,[l[97]||(l[97]=_("span",{class:"material-symbols-outlined !text-base text-emerald-500 dark:text-emerald-400 mt-0.5"},"check_circle",-1)),_("span",null,[_("strong",null,V(n.value?"Recent GK & Monthly Digest:":"সাম্প্রতিক সাধারণ জ্ঞান ও মাসিক ডাইজেস্ট:"),1),Qe(" "+V(n.value?"Quizzes on Bangladesh and international current affairs.":"বাংলাদেশ ও আন্তর্জাতিক বিষয়ক কুইজ।"),1)])])])]),_("div",Pg,[_("div",null,[_("span",Lg,V(n.value?"10% OFF on 3 months & 20% MEGA OFF on 6 months!":"৩ মাসে ১০% এবং ৬ মাসে ২০% মেগা ছাড়!"),1),_("span",Dg,V(n.value?"Affordable Monthly & Multi-Month Plans":"সাশ্রয়ী মাসিক ও কোয়ার্টারলি প্ল্যান"),1)]),Fe(Be(ke),{href:"/plans",class:"w-full sm:w-auto px-6 py-3.5 rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white text-xs font-bold shadow-lg shadow-emerald-500/20 text-center"},{default:Oe(()=>[Qe(V(n.value?"Start Job Bundle →":"জব বান্ডেল শুরু করুন →"),1)]),_:1})])])])]),_("section",Ig,[_("div",Ng,[_("div",null,[_("span",Ug,V(n.value?"Mentor-led learning · Courses":"মেন্টর পরিচালিত · অনলাইন কোর্স"),1),_("h2",Fg,V(n.value?"Top courses":"সেরা কোর্সসমূহ"),1),_("p",Og,V(n.value?"Follow courses taught by experienced mentors who explain each step clearly and keep lessons grounded in practice.":"অভিজ্ঞ মেন্টরদের পরিচালিত কোর্স করে প্রতিটি বিষয় সহজে ও বাস্তব অনুশীলনের মাধ্যমে আয়ত্ত করুন।"),1)]),Fe(Be(ke),{href:"/courses",class:"inline-flex items-center gap-2 text-sm font-semibold text-[var(--primary)] transition hover:text-[#ffb690]"},{default:Oe(()=>[Qe(V(n.value?"Explore all courses":"সব কোর্স দেখুন")+" ",1),l[99]||(l[99]=_("span",{class:"material-symbols-outlined"},"arrow_forward",-1))]),_:1})]),i.topCourses.length?(We(),qe("div",Bg,[(We(!0),qe(Xn,null,ti(i.topCourses,(y,E)=>{var R;return We(),qe("article",{key:y.id,class:"group flex flex-col justify-between overflow-hidden rounded-2xl border border-white/5 bg-[#1c1f2a]/80 shadow-lg transition duration-300 hover:-translate-y-1.5 hover:border-white/10 hover:shadow-2xl"},[_("div",null,[_("div",{class:en(["relative h-44 overflow-hidden bg-gradient-to-br",m[E%m.length]])},[y.cover_image_url?(We(),qe("img",{key:0,src:y.cover_image_url,alt:y.title,loading:"lazy",class:"absolute inset-0 h-full w-full object-cover transition duration-500 group-hover:scale-105"},null,8,kg)):(We(),qe("div",zg,[...l[100]||(l[100]=[_("span",{class:"material-symbols-outlined !text-5xl"},"school",-1)])])),_("span",Gg,V(y.level||(n.value?"COURSE":"কোর্স")),1)],2),_("div",Hg,[_("div",Vg,V(((R=y.mentor)==null?void 0:R.name)||(n.value?"Experienced mentor":"অভিজ্ঞ মেন্টর")),1),_("h3",Wg,V(y.title),1)])]),_("div",Xg,[_("span",qg,V(Number(y.price)===0?n.value?"Free":"ফ্রি":`৳${y.price}`),1),Fe(Be(ke),{href:`/courses/${y.slug}`,class:"text-xs font-semibold text-white transition hover:text-[var(--primary)]"},{default:Oe(()=>[Qe(V(n.value?"View course":"কোর্স দেখুন")+" →",1)]),_:1},8,["href"])])])}),128))])):Vt("",!0)]),_("section",Yg,[_("div",Kg,[_("div",null,[_("span",Zg,V(n.value?"Digital library · Read and explore":"ডিজিটাল লাইব্রেরি · পড়ুন ও জানুন"),1),_("h2",$g,V(n.value?"Latest books":"সর্বশেষ বই"),1),_("p",Jg,V(n.value?"Find a new book to read, revisit a familiar subject, or explore a topic that caught your attention.":"নতুন বই পড়ুন, পরিচিত বিষয় আবার দেখুন কিংবা আগ্রহ জাগানো কোনো বিষয় ঘুরে দেখুন।"),1)]),Fe(Be(ke),{href:"/library",class:"inline-flex items-center gap-2 text-sm font-semibold text-[#4cd7f6] transition hover:text-white"},{default:Oe(()=>[Qe(V(n.value?"Browse the library":"লাইব্রেরি দেখুন")+" ",1),l[101]||(l[101]=_("span",{class:"material-symbols-outlined"},"arrow_forward",-1))]),_:1})]),i.popularBooks.length?(We(),qe("div",Qg,[(We(!0),qe(Xn,null,ti(i.popularBooks.slice(0,5),(y,E)=>(We(),Ca(Be(ke),{key:y.id,href:`/library/${y.slug}`,class:"group overflow-hidden rounded-2xl border border-white/5 bg-[#1c1f2a]/80 shadow-lg transition duration-300 hover:-translate-y-1 hover:border-white/10 hover:shadow-2xl"},{default:Oe(()=>{var R;return[_("div",{class:en(["relative aspect-[3/4] overflow-hidden bg-gradient-to-br",x[E%x.length]])},[y.cover_image_url?(We(),qe("img",{key:0,src:y.cover_image_url,alt:y.title,loading:"lazy",class:"absolute inset-0 h-full w-full object-cover transition duration-500 group-hover:scale-105"},null,8,jg)):(We(),qe("div",ex,[...l[102]||(l[102]=[_("span",{class:"material-symbols-outlined !text-5xl"},"menu_book",-1)])])),y.is_free?(We(),qe("span",tx,V(n.value?"Free":"ফ্রি"),1)):Vt("",!0),l[103]||(l[103]=_("span",{class:"absolute inset-x-0 bottom-0 h-20 bg-gradient-to-t from-[#0a0e18]/80 to-transparent"},null,-1))],2),_("div",nx,[_("div",ix,V(y.level||(n.value?"DIGITAL BOOK":"ডিজিটাল বই")),1),_("h3",sx,V(y.title),1),_("div",rx,V(((R=y.author)==null?void 0:R.name)||(n.value?"Sikhun library":"Sikhun লাইব্রেরি")),1),y.total_pages?(We(),qe("div",ax,V(y.total_pages)+" "+V(n.value?"pages":"পৃষ্ঠা"),1)):Vt("",!0)])]}),_:2},1032,["href"]))),128))])):Vt("",!0)]),_("section",ox,[_("div",lx,[_("div",cx,[l[104]||(l[104]=_("span",{class:"material-symbols-outlined !text-sm text-emerald-400"},"verified",-1)),_("span",null,V(n.value?"Start with Free Plan — No payment required":"বিনামূল্যে ফ্রি প্ল্যান দিয়ে শুরু করুন — কোনো পেমেন্ট ছাড়াই ইনস্ট্যান্ট অ্যাক্টিভেশন"),1)]),_("h2",dx,V(n.value?"Choose the support that fits you":"আপনার উপযোগী সহায়তা বেছে নিন"),1),_("p",ux,V(n.value?"Compare the available plans and choose what works for your learning goals.":"উপলভ্য ৪টি প্ল্যান দেখে আপনার প্রয়োজন অনুযায়ী সেরা প্ল্যানটি বেছে নিন।"),1)]),i.plans.length?(We(),qe("div",fx,[(We(!0),qe(Xn,null,ti(i.plans,(y,E)=>(We(),qe("article",{key:y.id,class:en(["relative flex flex-col justify-between rounded-3xl border p-5 md:p-6 shadow-lg transition duration-300 hover:-translate-y-1",[y.slug==="pro"||Number(y.price_monthly)===199?"border-[#f97316]/50 bg-[#262a35] shadow-[0_24px_60px_rgba(0,0,0,.35)] ring-2 ring-[var(--primary)]":"border-white/5 bg-[#1c1f2a]/75"]])},[_("div",null,[_("div",hx,[_("h3",px,V(y.name),1),_("span",{class:en(["rounded bg-[#0a0e18] px-2 py-0.5 font-mono text-[9px] font-bold uppercase tracking-wider",y.slug==="pro"||Number(y.price_monthly)===199?"text-[#ff9a52]":"text-[var(--text-muted)]"])},V(Number(y.price_monthly)===0?"FREE":Number(y.price_monthly)===99?"STARTER":Number(y.price_monthly)===199?"POPULAR":"ELITE"),3)]),y.description?(We(),qe("p",mx,V(y.description),1)):Vt("",!0),_("div",gx,[_("div",xx,[_("span",{class:en(["font-heading text-3xl font-extrabold tracking-tight text-white",y.slug==="pro"||Number(y.price_monthly)===199?"text-[#ff9a52]":""])},"৳"+V(y.price_monthly),3),_("span",_x,V(n.value?"/ month":"/ মাস"),1)]),_("span",vx," ✓ "+V(Number(y.price_monthly)===0?n.value?"Lifetime Free Access":"লাইফটাইম ফ্রি অ্যাক্সেস":n.value?"Instant activation & full access":"ইনস্ট্যান্ট অ্যাক্টিভেশন ও ফুল অ্যাক্সেস"),1)]),_("div",Sx,[_("div",Mx,[l[105]||(l[105]=_("span",{class:"text-[9px] text-gray-500 block font-bold"},"AI CHAT",-1)),_("strong",null,V(y.ai_chat_minutes)+" "+V(n.value?"mins":"মিনিট"),1)]),_("div",bx,[l[106]||(l[106]=_("span",{class:"text-[9px] text-gray-500 block font-bold"},"VISION SOLVER",-1)),_("strong",null,V(y.ai_vision_limit||0)+" "+V(n.value?"images":"টি ছবি"),1)])]),_("div",yx,[(We(!0),qe(Xn,null,ti(Array.isArray(y.features)?y.features.slice(0,5):[],R=>(We(),qe("div",{key:R,class:"flex items-start gap-2"},[l[107]||(l[107]=_("span",{class:"material-symbols-outlined !text-sm text-[#10b981] mt-0.5"},"check_circle",-1)),_("span",Ex,V(R),1)]))),128))])]),Fe(Be(ke),{href:Number(y.price_monthly)===0?`/register?plan_id=${y.id}`:"/plans",class:en(["mt-6 flex w-full items-center justify-center gap-1.5 rounded-xl px-4 py-2.5 text-xs font-bold transition",y.slug==="pro"||Number(y.price_monthly)===199?"bg-gradient-to-r from-[#ff7a00] to-[#f97316] text-white shadow-lg shadow-[#f97316]/20":"bg-[#313540] text-[var(--text)] hover:bg-[#3b404d]"])},{default:Oe(()=>[Qe(V(Number(y.price_monthly)===0?n.value?"Start for free":"বিনামূল্যে শুরু করুন":n.value?"Choose Plan":"প্ল্যান বেছে নিন")+" ",1),l[108]||(l[108]=_("span",null,"→",-1))]),_:2},1032,["href","class"])],2))),128))])):Vt("",!0)]),(b=i.latestBlogs)!=null&&b.length?(We(),qe("section",Tx,[_("div",wx,[_("div",null,[_("span",Ax,V(n.value?"Insights · Science · AI · Education":"বিজ্ঞান · প্রযুক্তি · AI · শিক্ষা"),1),_("h2",Rx,V(n.value?"Latest from the blog":"সর্বশেষ ব্লগ আর্টিকেল"),1),_("p",Cx,V(n.value?"Read Bengali articles on science, technology, artificial intelligence, and study tips.":"বিজ্ঞান, প্রযুক্তি, কৃত্রিম বুদ্ধিমত্তা ও পড়াশোনার কৌশল নিয়ে আমাদের সাম্প্রতিক বাংলা আর্টিকেলগুলো পড়ুন।"),1)]),Fe(Be(ke),{href:"/blog",class:"inline-flex items-center gap-2 text-sm font-semibold text-[var(--primary)] transition hover:text-[#ffb690]"},{default:Oe(()=>[Qe(V(n.value?"Read all articles":"সব ব্লগ পড়ুন")+" ",1),l[109]||(l[109]=_("span",{class:"material-symbols-outlined"},"arrow_forward",-1))]),_:1})]),_("div",Px,[(We(!0),qe(Xn,null,ti(i.latestBlogs.slice(0,3),(y,E)=>(We(),Ca(Be(ke),{key:y.id,href:`/blog/${y.slug}`,class:"group flex flex-col overflow-hidden rounded-2xl border border-white/5 bg-[#1c1f2a]/80 shadow-lg transition duration-300 hover:-translate-y-1.5 hover:border-white/10 hover:shadow-2xl"},{default:Oe(()=>{var R,A;return[_("div",{class:en(["relative h-44 overflow-hidden bg-gradient-to-br",m[E%m.length]])},[y.featured_image_url?(We(),qe("img",{key:0,src:y.featured_image_url,alt:y.title,loading:"lazy",class:"absolute inset-0 h-full w-full object-cover transition duration-500 group-hover:scale-105"},null,8,Lx)):(We(),qe("div",Dx,[...l[110]||(l[110]=[_("span",{class:"material-symbols-outlined !text-5xl"},"article",-1)])]))],2),_("div",Ix,[_("div",null,[_("span",Nx,V(((R=y.category)==null?void 0:R.name_bn)||((A=y.category)==null?void 0:A.name_en)||(n.value?"Article":"আর্টিকেল")),1),_("h3",Ux,V(y.title),1),_("p",Fx,V(y.excerpt),1)]),_("span",Ox,[Qe(V(n.value?"Read":"পড়ুন")+" ",1),l[111]||(l[111]=_("span",{class:"material-symbols-outlined !text-xs"},"arrow_forward",-1))])])]}),_:2},1032,["href"]))),128))])])):Vt("",!0)])]}),_:1})],64))}};export{qx as default};
