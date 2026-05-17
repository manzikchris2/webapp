let currentOTPatempt = 0;



function show_pass(inp,hey){
    const pass = document.getElementById(inp)
    const eyes = document.getElementById(hey)
    if(eyes.classList.contains('fa-eye')){
        pass.type = 'text'
        eyes.classList.add('fa-eye-slash')
        eyes.classList.remove('fa-eye')
        
    }else if(eyes.classList.contains('fa-eye-slash')){
         pass.type = 'password'
        eyes.classList.remove('fa-eye-slash')
        eyes.classList.add('fa-eye')   
    }
}
function show_form(box) {
    const log = document.getElementById("login");
    const reg = document.getElementById("register");
    log.style.animation = "none;";
    reg.style.animation = "none";
    void log.offsetHeight;
    void reg.offsetHeight;
    if (box === 1) {
        log.style.animation = "shrink-box 1s ease 0s forwards";

        setTimeout(() => {
            reg.style.display = "flex";
            reg.style.animation = "shrink-box 1s ease 0s reverse";
            log.style.display = "none";
        }, 1000);
    } else if (box === 0) {
        reg.style.animation = "shrink-box 1s ease 0s forwards";

        setTimeout(() => {
            log.style.display = "flex";
            log.style.animation = "shrink-box 1s ease 0s reverse";
            reg.style.display = "none";
        }, 1000);
    }
}

function next_reg_box(current, action) {
    action = String(action);
    const current_box = document.getElementById("box" + current);
    current_box.style.animation = "none";
    void current_box.offsetHeight;
    const inputs = document.querySelectorAll("#box" + current + " input");
    const message = document.getElementById(current_box.id+'_message');
    let isvalid = true;
    console.log(inputs);
          inputs.forEach((inp) => {
            if(inp.value.length < 1 || inp.classList.contains('invalid')){
                message.innerText = 'please fill all the reqiured'
                isvalid = false
            }
            
        });

    if (action === "1" && isvalid) {
  

        let next = parseInt(current) + 1;
        const next_box = document.getElementById("box" + next);
        next_box.style.animation = "none";
        void next_box.offsetHeight;
        current_box.style.transformOrigin = "left";
        current_box.style.animation = "shrink-left 1s ease 0s forwards";
        next_box.style.transformOrigin = "right";
        setTimeout(() => {
            current_box.style.display = "none";
            next_box.style.display = "flex";
            next_box.style.animation = "open-right 1s ease 0s forwards";
        }, 1000);
    } else if (action === "-1") {
        let next = parseInt(current) - 1;
        const next_box = document.getElementById("box" + next);
        next_box.style.animation = "flex";
        void next_box.offsetHeight;
        current_box.style.transformOrigin = "right";
        current_box.style.animation = "open-right 1s ease 0s reverse";
        next_box.style.transformOrigin = "left";
        setTimeout(() => {
            current_box.style.display = "none";
            next_box.style.display = "flex";
            next_box.style.animation = "shrink-left 1s ease 0s reverse";
        }, 1000);
    }
}

function show_main_log(action) {
    const box = document.getElementById("main_log_container");
    const box2 = document.getElementById("info-holder");
    box.style.animation = "none";
    void box.offsetHeight;
    if (action === 1) {
        box.style.transformOrigin = "top";
        box.style.animation = "shrink-box 1s ease 0s forwards";
        setTimeout(() => {
            box.style.display = "none";
            box2.style.display = "flex";
        }, 1000);
    } else if (action === 0) {
        box.style.transformOrigin = "bottom";
        box2.style.display = "none";
        box.style.display = "block";
        box.style.animation = "shrink-box 1s ease 0s reverse";
    }
}
function show_side(action){
    const side = document.getElementById('side_menu');
    const main = document.getElementById('main-coontent');
    side.style.animation = 'none'
    void side.offsetHeight;
    if(action){
        side.style.display = 'block'
        side.style.animation='slide-from-left 1s ease 0s forwards'
        main.style.pointerEvents = 'none'
        
    }else{
        side.style.animation='slide-from-left 1s ease 0s reverse'
        setTimeout(()=>{
            main.style.pointerEvents = 'none'
            side.style.display = 'none'
        },1000)
    }

}

// input

document.addEventListener("input", function (e) {
    if (e.target.id === "name" || e.target.id === "s-name") {
        const value = e.target.value;
        const message = document.getElementById("");
        if (value.length == 0) {
            e.target.classList.add("invalid");
        } else if (/[!@#$%^&*]/.test(value)) {
            e.target.classList.add("invalid");
        } else {
            e.target.classList.remove("invalid");
        }
    }
    if (e.target.id === "email") {
        const email = e.target.value;
        let isvalid = email.includes("@") && email.includes(".");
        if (!isvalid) {
            e.target.classList.add("invalid");
        } else {
            e.target.classList.remove("invalid");
        }
    }
    if (e.target.id === "password") {
        const pass = e.target.value;
        const err = document.getElementById("box3_message");
        if (pass.length >= 8) {
            let hascap = /[A-Z]/.test(pass);
            let hasnum = /[0-9]/.test(pass);
            let haschar = /[!@#$%^&*]/.test(pass);
            if (hascap && hasnum && haschar) {
                e.target.classList.remove("invalid");
                err.classList.add("hidden");
                err.innerHTML = "";
            } else {
                err.innerHTML = "Password must contain:<br>";
                if (!hascap) {
                    err.innerHTML += "- Uppercase letter<br>";
                }
                if (!hasnum) {
                    err.innerHTML += "- Number<br>";
                }
                if (!haschar) {
                    err.innerHTML += "- Special character<br>";
                }
                e.target.classList.add("invalid");
                err.classList.remove("hidden");
            }
        } else {
            err.innerHTML = "Password must be at least 8 characters long";
            e.target.classList.add("invalid");
            err.classList.remove("hidden");
        }
    }
    if (e.target.id === "c-password") {
        const pass = document.getElementById("password").value;
        if (pass && pass === e.target.value) {
            e.target.classList.remove("invalid");
        } else {
            e.target.classList.add("invalid");
        }
    }
    if(e.target.id === 'tel-number'){
        const value = e.target.value
        if(!/^\d+$/.test(value)){
            e.target.classList.add('invalid')
        }else if(value.length < 10){
            e.target.classList.add('invalid')
        }else{
            e.target.classList.remove('invalid');
        }
    }
    if (e.target.classList.contains("otp_inp")) {
        const id = e.target.id;
            const current = parseInt(id.slice(-1));
        if (e.target.value.length == 1) {
            const next = current + 1;
            if (next <= 6) {
                const next_inp = document.getElementById("otp_inp" + next);
                next_inp.focus();
            }
        }else{
            const next = current -1;
            if(next >= 1){
                 const next_inp = document.getElementById("otp_inp" + next);
                 next_inp.focus();
            } 
        }
    }
});


window.addEventListener('click',function(e){ 
    console.log(e.target)
    if(e.target.classList.contains('order_box')){
        const id = e.target.id;
        const inner_box = document.getElementById(id+'_div');
        e.target.classList.toggle('active_order')
        inner_box.classList.toggle('active_inner');
    }
    if(e.target.id === 'home_btn'){
        const box = document.getElementById('main-coontent')
        const box2 = document.getElementById('show_orders')
        box.style.display='flex'
        box2.style.display='none'
    }
    
})

window.addEventListener('click',async(e)=>{

    try{ 
    if(e.target.classList.contains('order_span')){
        const id = encodeURIComponent(e.target.id)
        const response = await fetch('http://localhost:80/deliver/accept/'+id)
        if(!response.ok){throw new Error('failed to fetch accept')}
        const resp = await response.json();
        if(resp.success){
           
            await get_orders();
        }else{
            if(resp.head){
                window.location.replace(resp.head)
            } if(resp.message){
                alert(resp.message) 
                await get_current_order() 
            }
        }
    }

}catch(Error){
    console.log(Error)
}

})

window.addEventListener('submit',function(e){
    e.preventDefault();
})

//asyn fumctions

//register function
async function get_function(path,box_id){
    try{
        const box = document.getElementById(box_id);
        const response = await fetch('http://localhost:80'+path)
        if(!response.ok){throw new Error('failed to fetch'+box_id)}
        const resp = await response.json();
        if(resp.success){
            if(resp.content){
                box.innerHTML
            }
        }
    }catch(Error){
        console.log(Error)
    }
}
async function register(){
    let data = {}
    const inputs = document.querySelectorAll('#register_form input')
    const selct = document.getElementById('tel-prefix').value
    data['prefix'] = selct
    inputs.forEach(inp=>{
        let id = inp.id 
        let value =inp.value
        data[id] =  value ;
       
    })
    const message = document.getElementById('box4_message_box');
    try{
        const response = await fetch('http://localhost:80/deliver/register',{
            method:'POST',
            headers:{'content-type':'application/json'},
            body: JSON.stringify(data)
        })
        if(!response.ok){throw new Error('failed to fetch register')}
        const resp = await response.json();
        if(resp.success){
            message.innerText='registered succefully'
            setTimeout(()=>{
                show_form(0)
            },1000);

        }else{
            message.classList.add('invalid');
            message.innerText(resp.message);

        }
    }catch(Error){
        console.log(Error)
    }
}
async function login(){
    let data = {}
    const inps = document.querySelectorAll('#login-form input')
    inps.forEach(inp => {
        data[inp.id] = inp.value
    })
    console.log(data)
    try{
        const response = await fetch('http://localhost:80/deliver/login',{
            method:'POST',
            headers:{'content-type':'application/json'},
            body: JSON.stringify(data)
        })
        if(!response.ok){throw new Error('failed to fetch login')}
        const resp = await response.json()
        if(resp.success){
            const box =document.getElementById('login')
            box.innerHTML = resp.content
        }else{
            const message = document.getElementById('login_message')
            message.innerText = resp.message;
        }
    }catch(Error){
        console.log(Error)
    }
}
async function otp_management(action) {
    currentOTPatempt += 1;
    const message = document.getElementById("otp_message");
    let data = {};
    if (action === 1) {
        
        data = { resend: true };
    } else if (action === 0) {
        console.log("false");
        let otpcode = "";
        for (let i = 1; i < 7; i++) {
            otpcode += document.getElementById("otp_inp" + i).value;
            console.log(
                "value" +
                    i +
                    " : " +
                    document.getElementById("otp_inp" + i).value,
            );
        }
        if (otpcode.length < 6) {
            message.style.display = "inline";
            message.innerText = "please use a valid otp";
            return;
        } else {
            data = { otp: otpcode };
        }
    }
    try {
        const response = await fetch(
            "http://localhost:80/deliver/OTP/" + currentOTPatempt,
            {
                method: "POST",
                headers: { "content-type": "application/json" },
                body: JSON.stringify(data),
            },
        );
        if (!response.ok) {
            throw new Error("failed to fetch");
        }
        const resp = await response.json();
        if (resp.success) {
            window.location.replace(resp.page);
        } else {
            message.style.display = "inline";
            message.innerText = resp.message;
            if(resp.head){
                window.location.replace(resp.head);
            }

        }
    } catch (Error) {
        console.log("otp" + Error);
    }
}
async function get_orders(){
    try{
        const response= await fetch('http://localhost:80/deliver/retive')
        if(!response.ok){throw new Error('failed to get orders')}
        const resp = await response.json()
        if(resp.success){
            const box = document.getElementById('show_orders');
            const box2 = document.querySelector('#main-coontent')
            box2.style.display='none'
            box.style.display='block'
            box.innerHTML = resp.content

        }else{
            if(resp.head){
                window.location.replace(resp.head);
            }
        }
    }catch(Error){
        console.log(Error)
    }
}
async function logout() {
    show_side(false)
    const btn = document.getElementById('logout')
    btn.innerText="bye bye"
    try {
        const response = await fetch("http://localhost:80/deliver/logout");
        if (!response.ok) {
            throw new Error("wtf just happened");
        }
        const resp = await response.json();
        if (resp.success) {

            setTimeout(() => {
                window.location.replace("/" + resp.redirect);
            }, 2000);
        }
    } catch (Error) {
        console.log("error in log out" + Error);
    }
} 
async function get_current_order(){
    try{
        const box = document.getElementById('main-coontent');
        const main = document.getElementById('show_orders')
        box.style.display = 'none'
        main.style.display = 'flex'
        
        const response = await fetch('http://localhost:80/deliver/current_orders')
        if(!response.ok){throw new Error('failed to fetch current order')}
        const resp = await response.json()
        if(resp.success){
            if(resp.content){
                main.innerHTML = resp.content
            }
        }else{
                if(resp.head){
                    window.location.replace(resp.head)
                }else if(resp.message){
                    main.innerText=resp.message
                    console.log(resp.message)
                } 
            }

    }catch(Error){
        console.log(Error)
    }
}
async function delivered(){
    try{
        const response= await fetch('http://localhost:80/deliver/done')
        if(!response.ok){throw new Error('failed to fetch done')}
        const resp = await response.json();
        if(resp.success){
            setTimeout(async()=>{
                await get_orders();
            },1000)

        }else{
            if(resp.head){
                window.location.replace(resp.head);
            }
        }
    }
    catch(Error){
        console.log(Error)
    }
}

async function get_orders_history(){
    try{
        const box = document.getElementById('main-coontent')
        const main = document.getElementById('show_orders')
        const response= await fetch('http://localhost:80/deliver/history')
        if(!response.ok){throw new Error('failed to fetch done')}
        const resp = await response.json();
        if(resp.success){
            if(resp.content){
                main.innerHTML=resp.content
                main.style.display='flex'
                box.style.display='none'
            }else{
                main.innerHTML='<p class="message"> you have no orders yet </p>'
                main.style.display='flex'
                box.style.display='none' 
            }
            

        }else{
            if(resp.head){
                window.location.replace(resp.head);
            }
        }
    }
    catch(Error){
        console.log(Error)
    }
}