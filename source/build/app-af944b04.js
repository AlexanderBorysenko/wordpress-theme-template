const r=()=>{const e=document.querySelector(".header");if(!e)return;new ResizeObserver(t=>{for(let n of t){const o=n.contentRect.height;document.documentElement.style.setProperty("--header-height",`${o}px`)}}).observe(e)};window.addEventListener("load",()=>{r()});