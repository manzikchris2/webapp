function getcookies(name) {
    const cookies = {};
    document.cookie.split(";").forEach((cookie) => {
        const [cookiname, value] = cookie.trim().split("=");
        cookies[cookiname] = decodeURIComponent(value);
    });
    return cookies[name] || null;
}

function move_lables() {
    const labels = querySelector(".num-labels");
    labels.style.fontSize = "10px";
    labels.style.top = "15px";
    labels.style.left = "5px";
}

function card_brand(card) {
    const card_n = Number(card);
    console.log(card_n);
    if (card_n >= 4000 && card_n < 5000) {
        return "visa";
    }
    if (card_n >= 5100 && card_n < 5600) {
        return "mastercard-logo";
    } else {
        return null;
    }
}

async function set_address() {
    const box1 = document.getElementById("saved_address");
    const box2 = document.getElementById("r_address");
    console.log("1 " + box1.checked);
    console.log("1 " + box2.checked);
    let inp;
    let valid = false;
    if (box1.checked) {
        inp = decodeURIComponent(getcookies("c_address"));
        valid = true;
        console.log(inp);
    }
    if (box2.checked) {
        inp = document.getElementById("adress_in").value;
        const pttr = /^[A-Za-z0-9\s,.-]+$/;
        if (pttr.test(inp)) {
            valid = true;
        }
    }
    if (!box1.checked && !box2.checked) {
        alert("yububu");
    }
    if (valid) {
        try {
            const response = await fetch(
                "http://localhost:80/customer/address",
                {
                    method: "post",
                    headers: { "content-type": "application/json" },
                    body: JSON.stringify({ address: inp }),
                },
            );
            if (!response.ok) {
                throw new Error("fallllllied");
            }
            const resp = await response.json();
            if (resp.success) {
                const body = document.getElementById("card_box");
                const box = document.getElementById("adress_box");
                body.style.display = "flex";
                box.style.display = "none";
            } else {
                alert(resp.message);
            }
        } catch (Error) {
            console.log(Error);
        }
    } else {
        alert("enter a valid address");
    }
}
async function payment(order_id) {
    try {
        const response = await fetch("http://localhost:80/payment/order", {
            method: "POST",
            headers: { "content-type": "application/json" },
            body: JSON.stringify({ order: order_id }),
        });
        if (!response.ok) {
            throw new Error("failed to fetch");
        }
        const resp = await response.json();
        const old_box = document.getElementById("saved_cards");
        const pay_btn = document.getElementById("pay-btn");
        old_box.innerHTML = "";
        if (resp.success && resp.cards) {
            console.log(resp.cards);

            Object.entries(resp.cards).forEach(([key, card]) => {
                old_box.innerHTML += `<label class="card-option">
                                      <input type="radio" class="cards" name="cards" value="${key}">
                                      <div class="card_display">
                                         <p>${card.number}</p>
                                         <img src="https://img.icons8.com/color/48/000000/${card.brand}.png" class="saved_img">
                                      </div>
                                    </label>
        `;
            });
        } else {
            old_box.innerHTML = `<h3>NO CARdS saved</h3>`;
        }
        pay_btn.innerText = "pay( " + resp.total + "€ )";
    } catch (ERROR) {
        console.log("error in payment" + ERROR);
    }
}

async function pay() {
    const box1 = document.getElementById("new_card");
    const box2 = document.getElementById("old_cards");
    let value = {};
    if (!box1.checked && !box2.checked) {
        alert("pay up bitch");
        return null;
    }
    if (box2.checked) {
        let data = document.querySelector('input[name="cards"]:checked');
        console.log(data);
        if (data) {
            value["payement_id"] = data.value;
            value["method"] = "old";
        }
    }
    if (box1.checked) {
        value["name"] = document.getElementById("card_name").value;
        value["number"] = document.getElementById("card_number").value;
        value["cvs"] = document.getElementById("card_cvs").value;
        let brand_n = value["number"].slice(0, 4);
        value["brand"] = card_brand(brand_n);
    }
    const response = await fetch("http://localhost:80/pay", {
        method: "POST",
        headers: { "content-type": "application/json" },
        body: JSON.stringify(value),
    });
    if (!response.ok) {
        throw new Error("failed to fecth");
    }
    const resp = await response.json();
    if (resp.success) {
        const body = document.getElementById("card_box");
        body.innerHTML = "";
        body.innerHTML = "<h>payment succefull</h>";

        setTimeout(() => {
            document.cookie = "c_address= ; path=/"
            document.cookie =  "order_id= ; path=/"
            window.location.replace("home");
        }, 3000);
    } else {
        alert(resp);
    }
}
document.addEventListener("change", function (e) {
    const old_box = document.getElementById("saved_cards");
    const new_box = document.getElementById("new_cards");
    (old_box, (new_box.style.animation = ""));
    void old_box.offsetHeight;
    void new_box.offsetHeight;
    const random_a = document.getElementById("r_address_box");
    const saved_a = document.getElementById("s_adress_box");
    const subm = document.getElementById("submit");
    const div1 = document.getElementById("s_adress_box");
    const div2 = document.getElementById("r_address_box");
    if (e.target.id === "old_cards") {
        if (e.target.checked) {
            old_box.style.animation = "shrink_box 1s linear 0s reverse";
            old_box.style.display = "flex";
            new_box.style.display = "none";
        }
    }
    if (e.target.id === "new_card") {
        if (e.target.checked) {
            console.log("www");
            old_box.style.display = "none";
            new_box.style.animation = "shrink_box 0.5s linear 0s reverse";
            new_box.style.display = "flex";
        }
    }
    if (e.target.id === "saved_address") {
        if (e.target.checked) {
            subm.style.display = "block";
            const p = document.getElementById("s_address_p");
            const p2 = document.getElementById("r_address_p");
            p.style.textDecoration = "underline";
            p2.style.textDecoration = "none";
            const address = decodeURIComponent(getcookies("c_address"));
            div1.style.display = "flex";
            div1.innerHTML = `<p id=""home_address>${address}</p>`;
            div2.style.display = "none";
            subm.value = true;
        }
    }
    if (e.target.id === "r_address") {
        if (e.target.checked) {
            subm.style.display = "block";
            const p = document.getElementById("r_address_p");
            const p1 = document.getElementById("s_address_p");
            p.style.textDecoration = "underline";
            p1.style.textDecoration = "none";
            console.log("new");
            div1.style.display = "none";
            div2.style.display = "flex";
            subm.value = true;
        }
    }
});

document.addEventListener("click", function (e) {
    if (e.target.id === "card_name") {
        const label = document.getElementById("card_name_label");
        label.classList.add("label_active");
    }
    if (e.target.id === "card_cvs") {
        const label = document.getElementById("card_cvs_label");
        label.classList.add("label_active");
    }
    if (e.target.id === "card_date") {
        const label = document.getElementById("card_date_label");
        label.classList.add("label_active");
    }
    if (e.target.id === "card_number") {
        const label = document.getElementById("card_number_label");
        label.classList.add("label_active");
    }
    if (e.target.id === "pay-btn") {
        pay();
    }
    if (e.target.id === "submit") {
        set_address();
    }
});

document.addEventListener("input", function (e) {
    if (e.target.id === "card_cvs") {
        if (e.target.value.length > 3) {
            e.target.value = e.target.value.slice(0, 3);
            e.target.classList.remove("invalid");
        } else {
            e.target.classList.add("invalid");
        }
    }
    if (e.target.id === "card_number") {
        let value = e.target.value.replace(/\D/g, "");
        if (value.length < 16) {
            e.target.classList.add("invalid");
        }else{
            e.target.classList.remove("invalid");
        }
        if (value.length == 4) {
            let brand = card_brand(value);
            console.log(brand + " " + value);
            const img = document.getElementById("card-image");
            img.style.display = 'block'
            img.src =
                "https://img.icons8.com/color/48/000000/" + brand + ".png";
        }
        value = value.slice(0, 16);
        let formatted = "";
        for (let i = 0; i < value.length; i++) {
            if (i > 0 && i % 4 === 0) {
                formatted += "-";
            }
            formatted += value[i];
        }
        e.target.value = formatted;
        
    }
    if (e.target.id === "card_date") {
        const nay = new Date()
        const c_month = Number(String(nay.getMonth() + 1).padStart(2,"0"))
        const c_year = Number(String(nay.getFullYear()).slice(-2))

        let value = e.target.value.replace(/\D/g, "");
        value = value.slice(0, 4);
        let formated = "";
        for (let i = 0; i < value.length; i++) {
            if (i > 0 && i % 2 === 0) {
                formated += "/";
            }
            formated += value[i];
        }
        e.target.value = formated;

        if (value.length >= 2) {
            let month = parseInt(value.slice(0, 2));
            if (month < 1 || month > 12 || month < c_month ) {
                e.target.classList.add("invalid");
            } else {
                e.target.classList.remove("invalid");
            }
        }
        if(value.length >= 4){
            let year = parseInt(value.slice(2,4))
            if(year < c_year){
                e.target.classList.add("invalid");
            }else{
                e.target.classList.remove("invalid");
            } 

        }
    }
});

document.addEventListener("DOMContentLoaded", function (e) {
    const order = getcookies("order_id");
    if (order) {
        payment(order);
    } else {
        window.location.replace('/home')
    }
});

document.addEventListener("submit", function (e) {
    e.preventDefault();
});
