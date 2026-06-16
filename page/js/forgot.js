function get_mail(){
    console.log(window.location.pathname)
    const part = window.location.pathname.split('/')
    const email = decodeURIComponent(part.pop())
    const origin = decodeURIComponent(part.pop())
    return {email:email,origin:origin}
}


function getcookies(name) {
    const cookies = {};
    document.cookie.split(";").forEach((cookie) => {
        const [cookiname, value] = cookie.trim().split("=");
        cookies[cookiname] = decodeURIComponent(value);
    });
    return cookies[name] || null;
}

function show(id) {
    const inp = document.getElementById(id);
    const span = document.getElementById("show_" + id);
    if (inp.type === "password") {
        inp.type = "text";
        span.classList = "fas fa-eye-slash";
    } else {
        inp.type = "password";
        span.classList = "fas fa-eye";
    }
}

document.addEventListener("click", async (e) => {
    if (e.target.id === "submit") {
        try {
            const email = document.getElementById("email");
            if (email.classList.contains("invalid")) {
                return;
            }
           
            const path = window.location.pathname.split('/')
           const origin = decodeURIComponent(path.pop())
            const response = await fetch("http://localhost:80/forgot/check", {
                method: "post",
                header: { "content-type": "application/json" },
                body: JSON.stringify({ email: email.value, origin: origin }),
            });
            if (!response.ok) {
                throw new Error("failed");
            }
            const resp = await response.json();
            const mess = document.getElementById("message");
            mess.style.display = "block";
            if (resp.success) {
                const box = document.getElementById('content_box')
                box.innerHTML = "<h2>an email was sent please follow instructions</h2>"
                document.cookie = `origin=''; max-age=-1,path=/`
                setTimeout(()=>{
                    window.location.replace('/welcome')
                },5000)
            } else {
                mess.innerText = "email doesnt exist";
            }
        } catch (Error) {
            console.log(Error);
        }
    }

    if(e.target.id === 'change'){
        const pass = document.getElementById('pass')
        const c_pass = document.getElementById('c_pass')
        const mes = document.getElementById('message_r') 
        if(c_pass.classList.contains('invalid') || pass.classList.contains('invalid')){
            mes.innerText = "please validate your password"
            return;

        }
        let origin = get_mail()
        origin.pass = pass.value
        try{
            const response = await fetch('http://localhost:80/change/'+origin.origin,{
                method:"post",
                headers:{"content-type":"application/json"},
                body:JSON.stringify(origin)
            })
            if(!response.ok){throw new Error('failed to fetch')}
            const resp = await response.json()
            if(resp.success){
                const box = document.getElementById('container_box')
                box.innerHTML=`pssword reset please login`;
                setTimeout(()=>{
                     window.location.replace('/welcome')
                },5000)
                
                
            }
            else{
                alert('issue occured');
                window.location.replace('/home')
            }

        }
        catch(Error){
            console.log("is"+Error)
        }
        
    }
});
document.addEventListener("input", function (e) {
    if (e.target.id === "email") {
        const err = document.getElementById("message");
        const email = e.target.value;
        let isvalid = email.includes("@") && email.includes(".com");
        if (!isvalid) {
            err.style.display = "block";
            err.innerHTML = "please enter a valid email";
            e.target.classList.add("invalid");
        } else {
            err.style.display = "none";
            e.target.classList.remove("invalid");
        }
    }
    if (e.target.id === "pass") {
        const pass = e.target.value;
        const err = document.getElementById("message_r");
        err.style.display = "block";
        if (pass.length >= 8) {
            let hascap = /[A-Z]/.test(pass);
            let hasnum = /[0-9]/.test(pass);
            let haschar = /[!@#$%^&*]/.test(pass);
            if (hascap && hasnum && haschar) {
                e.target.classList.remove("invalid");
                err.style.display = "none";
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
     if (e.target.id === "c_pass") {
            const c_pass = e.target.value;
            let err = document.getElementById("message_r");
            const pass = document.getElementById("pass").value;
            console.log(pass+":"+c_pass)
            if (pass.length > 0) {
                if (pass === c_pass) {
                    err.innerHTML = "";
                    e.target.classList.remove("invalid");
                    err.style.display = "none";
                } else {
                    err.innerHTML = "Password must the same";
                    e.target.classList.add("invalid");
                    err.style.display = "block";
                }
            }else{
                err.style.display="block"
                err.innerText = 'insert a password first'
            }
        }
});

document.addEventListener('submit',function(e){
    e.preventDefault();
})

