let image_path ;


//functions
function loader() {
    return "<div id='loader'></div>";
}
function getCookies(name) {
    const cookies = {};
    document.cookie.split(";").forEach((cookie) => {
        const [cookiname, value] = cookie.trim().split("=");
        cookies[cookiname] = decodeURIComponent(value);
    });
    return cookies[name] || null;
}
function swithb(id) {
    id = Number(id);
    let isvalid = true;
    const box1 = document.getElementById("box" + id);
    const box = document.getElementById("box" + (id + 1));
    const inputs = document.querySelectorAll("#box" + id + " input");
    console.log("box" + id);
    inputs.forEach((inp) => {
        err = document.getElementById(inp.id + "-err");
        if (inp.classList.contains("invalid") || inp.value.length <= 0) {
            isvalid = false;
            err.classList.remove("hidden");
        } else {
            err.classList.add("hidden");
        }
    });
    if (isvalid) {
        box1.classList.add("hidden");
        setTimeout(() => {
            box.classList.remove("hidden");
        }, 100);
    }
}

function switha(id) {
    id = Number(id);
    box1 = document.getElementById("box" + id);
    box = document.getElementById("box" + (id - 1));
    console.log("box" + id);
    box1.classList.add("hidden");
    setTimeout(() => {
        box.classList.remove("hidden");
    }, 100);
}
function showp(id) {
    pass = document.getElementById(id);
    if (pass.type === "password") {
        pass.type = "text";
    } else {
        pass.type = "password";
    }
}
function handle_img() {
    const img = document.getElementById("img_display");
    const file = document.getElementById();
}

//async functions
async function show_profile(){
    try{
        const response = await fetch('http://localhost:80/partner/get_profile')
        if(!response.ok){
            throw new Error('failed to get pic')
        }
        const resp = await response.json()
        if(resp.success){ 
        const img1 = document.getElementById('big_img') 
        const img2 = document.getElementById('small_img') 
        img1.src = '/page/image/partners/'+resp.content+'.jpg'
        img2.src ='/page/image/partners/'+resp.content+'.jpg';
    }else{
        if(resp.head){
            window.location.replace(resp.head)
        }
    }
    }catch(Error){
        console.log(Error)
    }
}
async function register_p(r_data) {
    const data = JSON.stringify(r_data);
    response = await fetch("http://localhost:80/register/partner", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
        },
        body: data,
    });
    if (!response) {
        reg_btn = document.getElementById("partner_registration_submit");
        reg_btn.innerHTML = "register";
        throw new Error("an erro occured in the retrive");
    }
    const reg_data = await response.json();
    console.log(reg_data);
    const message = reg_data.message;
    show = document.getElementById("box4");
    if (message === "suceesfully") {
        show.innerHTML = `<div id="sucess">
                                <i class="fa fa-check-circle"></i> 
                                <p class="message">REGISTRION SUCCEFUL PLEASE LOGIN</p>
                             </div>`;
        setTimeout(() => {
            log_box = document.getElementById("partner_login");
            reg_box = document.getElementById("partner-registation");
            reg_box.style.display = "none";
            log_box.style.display = "flex";
        }, 5000);
    } else {
        show.innerHTML = `<p class="message">${message}</p> <br> <span id="final-back" class="form_back_btn" onclick="switha(4)">BACK</span> `;
        document.addEventListener("click", function (e) {
            if (e.target.id === "final-back") {
                show.innerHTML = `<button type="submit" id="partner_registration_submit">REGISTER</button>`;
            }
        });
    }
}

async function login_p(data) {
    err = document.getElementById("log-err");
    try {
        response = await fetch("http://localhost:80/login/partner", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify(data),
        });
        if (!response) {
            throw new Error("failed to login");
        }
        const log_data = await response.json();
        if (log_data.sucess) {
            const box1 = document.getElementById("pat-image");
            const box2 = document.getElementById("partnet-login");
            box1.style.animation = "slide-left 4s ease 0s forwards";
            box2.style.animation = "slide-right 4s ease 0s forwards";
            image_path='/page/image/partners/'+log_data.image
            setTimeout(() => {
                document.location.replace(log_data.page);
            }, 4000);
        }
        if (log_data.error) {
            err.innerHTML = log_data.error;
            err.classList.remove("hidden");
        }
    } catch (Error) {
        console.log("error in login: " + Error);
    }
}

async function profile_display() {
    const box = document.getElementById("side-container");
    try {
        const response = await fetch("http://localhost:80/partner/profile");
        if (!response.ok) {
            throw new Error("failed to fetch");
        }
        const resp = await response.json();
        if (resp.success) {
            let div = "";

            if (resp.content) {

                box.innerHTML = resp.content;
            }
        }
    } catch (Error) {
        console.log("error" + Error);
    }
}
async function update_profile() {
    const data = {
        name: document.getElementById("customer_fname").value,
        surname: document.getElementById("customer_lname").value,
        email: document.getElementById("customer_email").value,
        tel: document.getElementById("customer_tel").value,
        pass: document.getElementById("customer_pass").value,
    };
    console.log(data);
    try {
        const response = await fetch(
            "http://localhost:80/partner/update_profile",
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
            //await profile_display();
        }
    } catch (Error) {
        console.log("failed to update profilre");
    }
}
async function add_prod() {
    const formdata = new FormData();
    const json_data = {};
    const cat = document.querySelector("#add_product-form select");
    json_data[cat.id] = cat.value;
    const form = document.querySelectorAll("#add_product-form input");
    form.forEach((inp) => {
        if (inp.type === "file") {
            formdata.append("product_img", inp.files[0]);
        } else {
            json_data[inp.id] = inp.value;
        }
    });
    formdata.append("metadata", JSON.stringify(json_data));
    try {
        response = await fetch("http://localhost/add_product", {
            method: "POST",
            body: formdata,
        });

        if (!response) {
            throw new Error("error in upload fetch");
        }
        let data = await response.json();
        if (data.success) {
            const box = document.getElementById("orders-mangment");
            const btn = document.getElementById("home");
            box.innerHTML = `<h2>Product inserted succesfully</h2>`;
            setTimeout(() => {
                btn.click();
            },2000);
        }
    } catch (Error) {
        console.log("error in upload: " + Error);
    }
}

async function get_prod() {
    try {
        const section = document.getElementById("orders-mangment");
        const response = await fetch("http://localhost/partner/update_products");
        
        if (!response.ok) {
            throw new Error("issue in get update");
        }
        const data = await response.json();
      
        if(data.success){
            if(data.content){
                section.innerHTML=data.content
            }
        }else{
            if(data.head){
                window.location.replace(data.head);
            }
        }
        
    } catch (Error) {
        console.log("err in post" + Error);
    }
}

async function update_prod(prod) {
    const tr = document.getElementById(prod);
    const name_inp = tr.querySelector('input[type="text"]');
    const stat_inp = tr.querySelector(".status");
    const cat_inp = tr.querySelector(".category");
    const price_inp = tr.querySelector('input[type="number"]');

    const data = {
        name: name_inp.value,
        price: price_inp.value,
        stat: stat_inp.value,
        id: prod,
        category: cat_inp.value,
    };
    try {
        const response = await fetch("http://localhost/product/update", {
            method: "POST",
            headers: { "content-type": "application/json" },
            body: JSON.stringify(data),
        });
        if (!response.ok) {
            throw new Error("failed to fecth");
        }
        const resp = await response.json();
        if (resp.success) {
            await get_prod();
        }
    } catch (Error) {
        console.log(Error);
    }
}
async function get_all() {
    try {
        const box = document.getElementById("orders-mangment");
        box.style.cssText =`flex-direction: row; 
                            flex-wrap: wrap; 
                            `;
        box.style.alignItems="stretch"
        const response = await fetch("http://localhost/partner/all");
        if (!response.ok) {
            throw new Error("failed to fecth");
        }
        const resp = await response.json();
        if (resp.success) {
            if (Object.keys(resp.content).length > 0) {
                const content = Object.entries(resp.content);
                let div = "";
                content.forEach(([key, value]) => {
                    div += `<div id="${key}" class="order_box">`;
                    div += `<h2>ORDER ${key}</h2>`;
                    value.products.forEach((prod) => {
                        div += `<h3> name:${prod.name}</h3>`;
                        div += `<p>quantity:${prod.quantity}</p>`;
                    });
                    div += `<div id='box-${key}' class="accept-box" ">`;

                    if (value.status && value.status.includes("ACCEPT")) {
                        div += `<button  id="btn-${key}" class="accept-btn" onclick="order_ready(${key})">ready?</button>`;
                    }

                    div += `</div>`;

                    div += ` </div>`;
                });
                box.innerHTML = div;
            } else {
                box.innerHTML = `<h2> you currentry have no order</h2>`;
            }
        }
    } catch (Error) {
        console.log("error in get_all " + Error);
    }
}

async function order_ready(id) {
    try {
        const btn = document.getElementById("btn-" + id);
        const response = await fetch("http://localhost/orders/accept", {
            method: "post",
            headers: { "content-type": "application/json" },
            body: JSON.stringify({ order: id, stat: "READY" }),
        });
        const resp = await response.json();
        if (resp.success) {
            btn.innerHTML = "Ready";
            setTimeout(async () => {
                await get_all();
            }, 2000);
        }
    } catch (ERROR) {
        console.log(ERROR);
    }
}

//order managment

async function order_managment() {
    try {
        response = await fetch("http://localhost/orders/partner");
        if (!response.ok) {
            throw new Error("error in order_managment");
        }
        const data = await response.json();
        console.log(data);
        const section = document.getElementById("assign-order");
    } catch (Error) {
        console.log("errror: " + Error);
    }
}

//eventlistenrs on click

document.addEventListener("click", function (e) {
    if (
        e.target.id === "partner-confirm" ||
        e.target.id === "register-button"
    ) {
        box = document.getElementById("partner_login");
        box1 = document.getElementById("partener_promo");
        box2 = document.getElementById("partner-registation");
        box1.style.display = "none";
        box2.style.display = "flex";
        if (!box.classList.contains("hidden")) {
            box.classList.add("hidden");
        }
    }
    if (e.target.id === "login-button") {
        box = document.getElementById("partner_login");
        box1 = document.getElementById("partener_promo");
        box2 = document.getElementById("partner-registation");
        box1.style.display = "none";
        box2.style.display = "none";
        box.classList.remove("hidden");
    }
    if (e.target.id === "partner_registration_submit") {
        e.target.innerHTML = loader();
        data = {};
        input = document.querySelectorAll("#partner-registration-form input");
        input.forEach((inp) => {
            data[inp.id] = inp.value;
        });
        try {
            message = register_p(data);
        } catch (Error) {
            console.log("error in reg:" + Error);
        }
    }
    if (e.target.id === "p-login-btn") {
        const data = {};
        input = document.querySelectorAll("#partnet-login input");
        input.forEach((inp) => {
            data[inp.id] = inp.value;
        });
        login_p(data);
    }
    if (e.target.id === "home") {
        const box1 = document.getElementById("orders-mangment");
        box1.classList.remove("hiiden");
        get_all();
    }
    if (e.target.id === "menu") {
        const side = document.getElementById("left-side");
        const content = document.getElementById("side-container");
        side.style.display = "block";
        content.innerHTML = `<button id="profile" class="side-btns"> profile</button>
                            <button id="logout" class="side-btns"> logout</button>`;
    }
    if (e.target.id === "close-btn") {
        const side = document.getElementById("left-side");
        side.style.display = "none";
    }
    if (e.target.id === "update") {
        get_prod();
    }
    if (e.target.id === "drop_area") {
        const inp = document.getElementById("imageInput");
        console.log("click");
        inp.click();
    }
    if (e.target.id === "profile") {
        profile_display();
    }
});
//drag-over

document.addEventListener("dragover", function (e) {
    if (e.target.id === "drop_area") {
        e.target.preventDefault;
        e.target.classList.add("drag_over");
    }
});

//drag_leave
document.addEventListener("dragleave", function (e) {
    if (e.target.id === "drop_area") {
        e.target.classList.remove("drag_over");
    }
});
//drop
document.addEventListener("drop", function (e) {
    if (e.target.id === "drop_area") {
        e.target.preventDefault;
        e.target.classList.remove("drag_over");
        file = e.dataTransfer.files;
    }
});
// change

document.addEventListener("change", function (e) {
    if (e.target.id === "imageInput") {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function (event) {
                const preview = document.getElementById("img_display");
                preview.style.backgroundImage = 'url("'+event.target.result+'")';
                preview.style.display = "block";
            };
            reader.readAsDataURL(file);
        }
    }
});

// event listener on input

document.addEventListener("input", function (e) {
    if (e.target.id === "p-email") {
        email = e.target.value;
        if (email.length > 0) {
            let isvalid = email.includes("@") && email.includes(".com");
            if (isvalid) {
                e.target.classList.remove("invalid");
            } else {
                e.target.classList.add("invalid");
            }
        }
    }
    if (e.target.id === "p-tel") {
        tel = e.target;
        let haschar =
            /[!@#$%^&*]/.test(tel.value) ||
            /[A-Z]/.test(tel.value) ||
            /[a-z]/.test(tel.value);
        err = document.getElementById(tel.id + "-err");
        if (tel.value.length != 10) {
            tel.classList.add("invalid");
            err.innerHTML = "tel-number must be ";
            err.classList.remove("hidden");
        } else {
            if (haschar) {
                tel.classList.add("invalid");
                err.innerHTML =
                    "tel-number must not contain letters or special chars";
                err.classList.remove("hidden");
            } else {
                err.innerHTML = "";
                err.classList.add("hidden");
                tel.classList.remove("invalid");
            }
        }
    }
    if (e.target.id === "p-pass") {
        pass = e.target.value;
        err = document.getElementById("p-pass-err");
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
    if (e.target.id === "p-confirm-pass") {
        pass = document.getElementById("p-pass");
        if (e.target.value === pass.value) {
            e.target.classList.remove("invalid");
        } else {
            e.target.classList.add("invalid");
        }
    }

    if(e.target.id === 'p-name'){
        const desti = document.getElementById('pname');
        desti.innerHTML = e.target.value
    }
    if(e.target.id === 'p-price'){
        const desti = document.getElementById('p_price');
        desti.innerHTML = e.target.value+' €'
    }
});
// input by class for table
// event listeners on summit forms

document.addEventListener("submit", function (e) {
    e.preventDefault();
    if (e.target.id === "add_product-form") {
        add_prod();
    }
});

document.addEventListener("DOMContentLoaded", function (e) {
    show_profile()
    document.addEventListener("click", function (e) {
        if (e.target.classList.contains("product_in")) {
            document.querySelectorAll(".product_in").forEach((input) => {
                input.style.border = "";
                input.style.backgroundColor = "";
            });

            e.target.style.border = "2px solid #73d5eb";
            e.target.style.backgroundColor = "white";
        }
    });
    const mess = document.getElementById("main-message");
    setTimeout(() => {
        mess.style.display = "none";
    }, 4400);
});

const orders = document.getElementById("orders-mangment");
orders.addEventListener("click", function (e) {
    const order_box = e.target.closest(".order_box");
    if (order_box && !e.target.closest(".accept-btn")) {
        const accept_box = order_box.querySelector(".accept-box");
        const order_id = order_box.id;
        if (accept_box && !accept_box.querySelector(".accept-btn")) {
            const btn = document.createElement("button");
            btn.className = "accept-btn";
            btn.id = `btn-${order_id}`;
            btn.textContent = "accept";

            btn.addEventListener("click", async (ev) => {
                ev.stopPropagation();
                if (btn.disabled) return;
                btn.disabled = true;
                btn.innerText = `processing...`;
                const response = await fetch("http://localhost/orders/accept", {
                    method: "post",
                    headers: { "content-type": "application/json" },
                    body: JSON.stringify({ order: order_id, stat: "ACCEPT" }),
                });
                const resp = await response.json();

                if (resp.success) {
                    setTimeout(async () => {
                        btn.innerText = "preparing.....";
                        await get_all();
                    }, 1000);
                }
            });
            accept_box.appendChild(btn);
        }
    }
});

const body = document.querySelector("body");

body.addEventListener("click", async (ev) => {
    const content = document.getElementById("orders-mangment");
    if (ev.target.id === "logout") {
        const request = await fetch("http://localhost/logout/partner");
        const resp = await request.json();
        if (resp) {
            document.location.replace(resp.redirect);
        }
    }
    if (ev.target.id === "add") {
        content.innerHTML = "";
        const response = await fetch("http://localhost:80/partner/categories");
        const resp = await response.json();
        if (resp.success){
            if(resp.content){
                content.innerHTML = resp.content
            }
           
        }else{
            if(resp.head){
                window.location.replace(resp.head);
            }
        }
    }
    if (ev.target.id === "history") {
        content.innerHTML = "";
        const response = await fetch("http://localhost:80/search/ready");
        const resp = await response.json();
        if (resp.success) {
            const contents = Object.entries(resp.content);
            let div = "";
            contents.forEach(([key, value]) => {
                div += `<div id="${key}" class="order_box">`;
                div += `<h2>ORDER ${key}</h2>`;
                value.products.forEach((prod) => {
                    div += `<h3> name:${prod.name}</h3>`;
                    div += `<p>quantity:${prod.quantity}</p>`;
                });
                div += `<h>assigned</h>`;

                div += ` </div>`;

                content.innerHTML = div;
                content.classList.add("horizontal");
            });
        }
    }
});



