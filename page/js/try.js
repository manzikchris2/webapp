document.addEventListener("click",function(e){
    if(e.target.id === "click-me"){
        console.log("am clicked");
    }
    

})  

function amaz(data){
    const body = document.getElementById("frame");
    const p_show = document.getElementById("productshow");
    body.style.opacity = "0.3";
    body.style.pointerEvents = "none";
    p_show.offsetHeight;
    p_show.innerHTML = "";
    p_show.style.zIndex="1000";
    setTimeout(()=>{
        p_show.style.opacity="1";
        p_show.innerHTML = `
        <div class="prods">
        <p> you the man big dawg you the man</p>
        <button id="click-me">click me</button>
        </div>
        `
    },1000);

}
const box = document.getElementById('box')
box.addEventListener('click',function(){
    this.style.display="none"
})
const money = document.getElementById('money')
money.addEventListener('click',function(){
    const box = document.getElementById('box')
    box.style.display="flex"
})