let isrestoring = false;
let currentOTPatempt = 0;
let current = 1;
const max = 3;
let interval;

function update_history(state, title, url) {
    if (isrestoring) {
        console.log("Skipping history update during restore");
        return;
    }

    if (history.pushState) {
        history.pushState(state, title, url);
    } else {
        window.location.hash = url;
    }
    sessionStorage.setItem(
        "lastState",
        JSON.stringify({
            state: state,
            url: url,
            timestamp: Date.now(),
        }),
    );
}

function loadHomeWithHistory() {
    const currentState = history.state;
    if (!currentState || currentState.page !== "home") {
        update_history({ page: "home" }, "home", "/home");
    }
    get_all(true);
}

function restore_page_state() {
    isrestoring = true;

    const saved = sessionStorage.getItem("lastState");

    if (saved) {
        try {
            const { state, url } = JSON.parse(saved);
            console.log("Parsed state:", state);

            if (state && state.page === "products" && state.category) {
                console.log(
                    "Restoring products from state, ID:",
                    state.category,
                );
                loadProducts(state.category, state.action, true); // Pass true for restore
            } else if (state && state.page === "cart" && state.orderId) {
                console.log("Restoring cart from state, ID:", state.orderId);
                go_to_cart(state.orderId, null, true);
            } else if (state && state.page === "search-result" && state.query) {
                console.log("Restoring search from state:", state.query);
                get_prod(state.query, state.type, true);
            } else if (state && state.page === "home") {
                console.log("Restoring home from state");
                get_all(true);
            }
        } catch (e) {
            console.log("Failed to parse saved state:", e);
        }
    } else {
        const path = window.location.pathname;
        console.log("No valid saved state, restoring from URL:", path);

        let match = path.match(/\/home\/category\/(\d+)/);
        if (match) {
            loadProducts(match[1], "cat", true);
            return true;
        }
    }

    setTimeout(() => {
        isrestoring = false;
        console.log("Restore complete");
    }, 100);
}
function loader(name) {
    const box = document.getElementById(name);
    box.innerHTML = '<div id="loader_container"><div id="loader"></div></div>';
}
function best_show(action) {
    const box = document.getElementById("product-show");
    const main = document.getElementById("main-show");
    if (action) {
        box.style.display = "none";
        main.classList.add("best_off");
    } else {
        box.style.display = "flex";
        main.classList.remove("best_off");
    }
}
function show(id, formid) {
    const form = document.getElementById(formid);
    if (form) {
        form.addEventListener("submit", function (e) {
            e.preventDefault;
        });
    }
    form.validation = "off";
    const pass_input = document.getElementById(id);
    if (pass_input.type === "password") {
        pass_input.type = "text";
    } else {
        pass_input.type = "password";
    }
}

function searchHandler(e) {
    console.log(e.target.id);
    const search = document.getElementById("search");
    const main = document.getElementById("main_view_all");
    if (e.target.id !== "search-show-content") {
        search.style.display = "none";
        main.style.zIndex = "";
        //hide_cat_side(false);
    }
}

function getcookies(name) {
    const cookies = {};
    document.cookie.split(";").forEach((cookie) => {
        const [cookiname, value] = cookie.trim().split("=");
        cookies[cookiname] = decodeURIComponent(value);
    });
    return cookies[name] || null;
}

function close_side(action) {
    const main = document.querySelector(".main-content");
    const body = document.querySelector("body");
    const side = document.getElementById("left-slide");
    const header = document.querySelector("header");
    side.style.animation = "none";
    void side.offsetHeight;
    if (action) {
        main.style.pointerEvents = "all";
        body.style.overflow = "scroll";
        side.style.animation = "slide-left 1s linear 0s reverse";
        side.style.zIndex = "0";
        setTimeout(() => {
            side.style.display = "none";
            header.style.zIndex = "1";
        }, 1000);
    } else {
        window.scrollTo({ top: 0, behavior: "smooth" });
        side.style.animation = "slide-left 1s linear 0s forwards";
        side.style.display = "flex";
        side.style.zIndex = "2000";
        header.style.zIndex = "0";
        main.style.pointerEvents = "none";
        body.style.overflow = "hidden";
    }
}

function hide_cat_side(action) {
    const side = document.getElementById("side_menu");
    const menu = ducument.getElementById("menu");
    side.style.animation = "none";
}

function hide_search(action) {
    const box = document.getElementById("search-show-content");
    box.style.animation = "none";
    void box.offsetHeight;
    if (action) {
        box.style.animation = "shrink-box 0.5s ease 0s forwards";
        box.style.display = "none";
    } else {
        box.style.display = "flex";
        box.style.animation = "shrink-box 0.5s ease 0s reverse";
    }
}

// product-show

function sidebar(a) {
    const side = document.getElementById("left-slide");

    if (Number(a) == 0) {
        get_cart();
    } else {
        const inside = document.getElementById("left-content");
        inside.innerHTML = "";
        inside.innerHTML = `<i class="fas fa-user"></i>
                            <button class="profile-btn" onclick="account()">profile</button>
                            <i class="fas fa-sign-out-alt"></i>
                            <button class="profile-btn" onclick="logout()">Logout</button>
                             `;
    }
    close_side(false);
}

function loadProducts(cat, action, fromRestore = false) {
    const box = document.getElementById("main-show");
    const menu = document.getElementById("menu");

    let req = {};

    if (action === "cat") {
        req = { cat_id: cat };
        // Only add to history if NOT from restore
        if (!fromRestore) {
            menu.click();
            console.log(
                "Adding history for category:",
                `/home/category/${cat}`,
            );
            update_history(
                { page: "products", category: cat, action: "cat" },
                "Category Products",
                `/home/category/${cat}`,
            );
        } else {
            console.log("Skipping history for restore");
        }
        by_category(req);
    }
    if (action === "part") {
        menu.click();
        req = { part_id: cat };

        if (!fromRestore) {
            console.log("Adding history for partner:", `/home/partner/${cat}`);
            update_history(
                { page: "products", category: cat, action: "part" },
                "Partner Products",
                `/home/partner/${cat}`,
            );
        } else {
            console.log("Skipping history for restore");
        }
        by_category(req);
    }
}

function trans_form(a) {
    const reg_div = document.getElementById("register-container");
    const log_div = document.getElementById("log-in");
    log_div.style.animation = "none";
    reg_div.style.animation = "none";
    void log_div.offsetHeight;
    void reg_div.offsetHeight;
    console.log(a);
    if (a === 1) {
        log_div.style.animation = "slide 1s linear 0s forwards";
        setTimeout(() => {
            reg_div.style.display = "flex";
            reg_div.style.animation = "slide-left 1s linear 0s forwards";
            log_div.style.display = "none";
        }, 500);
    } else {
        reg_div.style.animation = "slide-right-v2 1s linear 0s forwards";

        setTimeout(() => {
            log_div.style.display = "flex";
            log_div.style.animation = "slide-right 1s linear 0s forwards";
            reg_div.style.display = "none";
        }, 500);
    }
}
function hide_log() {
    const box4 = document.querySelector("footer");
    const box = document.getElementById("log-container");
    const box2 = document.getElementById("product-container");
    box.style.animation = "none";
    box.offsetHeight;
    box.style.animation = "shrink 1s linear 0s 1 forwards";
    box2.style.opacity = 1;
    box4.style.opacity = 1;
    (box4, (box2.style.pointerEvents = "all"));
}

function clear_timer() {
    clearInterval(interval);
    interval = setInterval(() => {
        transition(1);
    }, 30000);
}

// async functions

function management(action, frombtn) {
    let next = current + Number(action);
    if (frombtn) {
        clear_timer();
    }
    if (next == 0) {
        console.log("this");
        return;
    }
    if (next > max) {
        next = 1;
    }
    const box1 = document.getElementById("box_" + current);
    const box2 = document.getElementById("box_" + next);
    box1.style.animation = "none";
    box2.style.animation = "none";

    void box1.offsetHeight;
    void box2.offsetHeight;

    box1.style.animation = "fade_out 6s linear 0s forwards";

    setTimeout(() => {
        box2.style.display = "flex";
        box2.style.animation = "fade_in 6s linear 0s forwards";
        box1.style.display = "none";
        current = next;
    }, 5900);
}
 interval = setInterval(() => {
                    management(1);
                }, 20000);

async function box_management() {
    const box = document.getElementById("product-show");
    try {
        const response = await fetch("http://localhost:80/product/best");
        if (!response.ok) {
            throw new Error("cant fetch");
        }
        const resp = await response.json();
        const length = resp.content.length;
        const content = resp.content;

        for (let i = 0; i < length; i++) {
            // Show first box (index 0), hide others
            const displayStyle = i === 0 ? "flex" : "none";

            box.innerHTML += `
        <div id="box_${i + 1}" class="main_slide_box" style="display: ${displayStyle}">
            <img src="/page/image/partners/${content[i].image}"> 
            <h2>${content[i].Bname}</h2>
            <h3>${content[i].Address}</h3>
            <button onclick="loadProducts('${content[i].ID}','part')">visit</button>
        </div>
    `;
        }
    } catch (Error) {
        console.log(Error);
    }
}

async function login(demand, user, pass, err) {
    const requestdata = JSON.stringify({
        demand: demand,
        user: user,
        pass: pass,
    });
    try {
        response = await fetch("http://localhost:80/login", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: requestdata,
        });
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        const data = await response.json();
        console.log(data);
        if (data.success) {
            err.style.display = "none";
            const box = document.getElementById("log-container");
            loader("log-container");
            setTimeout(() => {
                box.innerHTML = data.page;
               
            }, 1000);
        } else {
            err.classList.remove("hidden");
            if (data.message) {
                err.innerText = "invalid username or password";
            }
            err.innerText = "invalid username or password";
        }
    } catch (Error) {
        console.error("Error fetching data:", Error);
    }
}
async function otp_management(action) {
    currentOTPatempt += 1;
    const message = document.getElementById("otp_message");
    let data = {};
    if (action === 1) {
        console.log("true");
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
            "http://localhost:80/customer/OTP/" + currentOTPatempt,
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
            console.log(resp);
        }
    } catch (Error) {
        console.log("otp" + Error);
    }
}
async function register(data) {
    try {
        response = await fetch("http://localhost:80/register/customer", {
            method: "post",
            header: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify(data),
        });
        if (!response.ok) {
            throw new Error("failed to fetch regisester");
        }
        const respdata = await response.json();
        if (respdata.success) {
            let err = document.getElementById("c-f-main_err");
            err.classList.remove("hidden");
            err.innerHTML = "Registration succefull please login";
            setTimeout(() => {
                trans_form(0);
            }, 3000);
        } else {
            let err = document.getElementById("c-f-main_err");
            err.classList.remove("hidden");
            err.innerHTML = respdata.message;
        }
    } catch (Error) {
        console.log("error in registration: " + Error);
    }
}
async function account() {
    const box = document.getElementById("left-content");
    try {
        const response = await fetch("http://localhost:80/customer/profile");
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
        console.log("failed account");
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
    try {
        const response = await fetch(
            "http://localhost:80/customer/update_profile",
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
            await account();
        }
    } catch (Error) {
        console.log("failed to update profilre");
    }
}
async function fetchcat() {
    const box = document.getElementById("cat_extra_box");
    try {
        const response = await fetch("http://localhost:80/categories");
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        const data = await response.json();

        box.innerHTML = "";
        if (data.success) {
            box.innerHTML = data.content;
        }
    } catch (error) {
        console.error("Error fetching data:", error);
    }
}
fetchcat();
async function by_category(req) {
    const box = document.getElementById("main-show");
    try {
        let response = await fetch("http://localhost/products/category", {
            method: "post",
            headers: {
                "content-type": "application/json",
            },
            body: JSON.stringify(req),
        });
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        const data = await response.json();
        console.log(data);
        if (data.success) {
            best_show(true);
            if (data.products && data.products.length > 0) {
                box.innerHTML = data.products;
                box.style.justifyContent = "";
            } else {
                box.innerHTML = `<h2 class="empty-q">No Products in this category</h2>`;
            }
        }
    } catch (error) {
        console.error("Error fetching data:", error);
    }
}

async function get_all(fromPopstate = false) {
    best_show(false);
    try {
        const box = document.getElementById("main-show");
        box.style.display = "";
        box.style.flexDirection = "";
        box.style.flexWrap = "";
        box.style.justifyContent = "";

        const response = await fetch("http://localhost:80/products/all");
        if (!response.ok) {
            throw new Error("error");
        }
        const resp = await response.json();
        if (resp.success) {
            box.innerHTML = resp.products;
        }
    } catch (Error) {
        console.log("error in get all: " + Error);
    }
}

async function price_maunupuration(name, order, value) {
    const inp = document.getElementById(name + "_inp");
    const result = Number(inp.value) + Number(value);
    const part = window.location.pathname.split("/");
    const pname = part.pop();
    if (result < 1) {
        return;
    }

    console.log(result);
    try {
        const response = await fetch(
            "http://localhost:80/order/quantity_change",
            {
                method: "post",
                headers: { "content-type": "application/json" },
                body: JSON.stringify({
                    quantity: result,
                    id: order,
                    pname: name,
                }),
            },
        );
        if (!response.ok) {
            throw new Error("failled to fetch");
        }
        const resp = await response.json();
        if (resp.success) {
            if (history.state.orderId) {
                await go_to_cart(history.state.orderId, pname);
            } else {
                alert("order_id lost");
                exit();
            }
        }
    } catch (Error) {
        console.log(Error);
    }
}

async function add_to_cart(bname, prod_id) {
    try {
        let data = { partner: bname, product: prod_id };
        const response = await fetch("http://localhost:80/order", {
            method: "post",
            header: {
                "content-type": "application/json",
            },
            body: JSON.stringify(data),
        });
        if (!response.ok) {
            throw new Error("faild to fetch");
        }
        const ret_data = await response.json();
        if (ret_data.sucess) {
            await get_cart();
            console.log("we made it");
        }
    } catch (Error) {
        console.log("error in add_cart: " + Error);
    }
}
async function get_cart() {
    const side = document.getElementById("left-content");
    side.innerHTML = "";
    try {
        const response = await fetch("http://localhost:80/order/cart");
        if (!response.ok) {
            throw new Error("failed to fetch");
        }

        let data = await response.json();
        cart = "";

        if (data.success && data.better) {
            //issues to slove instead of using id use name in remove update the quer by join the partners table
            side.innerHTML = data.better;
        } else {
            side.innerHTML = "<h3>your cart is empty </h3>";
            console.log(data.better);
        }
    } catch (Error) {
        console.log("error in get_cart" + Error);
    }
}
async function remove_cart(id, name) {
    try {
        let data = { p_id: id, Name: name };
        const response = await fetch("http://localhost:80/order/delete", {
            method: "post",
            headers: { "content-type": "Application/json" },
            body: JSON.stringify(data),
        });
        if (!response.ok) {
            throw new Error("failed to fetch");
        }
        const resp = await response.json();
        console.log(resp);
        if (resp.success) {
            await get_cart();
        }
    } catch (Error) {
        console.log("error in remove_cart" + Error);
    }
}

async function logout() {
    close_side(true);
    try {
        const response = await fetch("http://localhost:80/logout/customer");
        if (!response.ok) {
            throw new Error("wtf just happened");
        }
        const resp = await response.json();
        if (resp.success) {
            update_history({ page: "logout" }, "Logged Out", "/");
            const main = document.getElementById("main_view_all");
            const header = document.querySelector("header");
            const div = document.getElementById("side_menu_content");
            div.innerHTML = "";
            div.innerHTML = `<h2 id="good_bye">GooD ByeE</h2>`;

            main.style.animation = "none";
            header.style.animation = "none";
            void main.offsetHeight;
            void header.offsetHeight;

            main.style.animation = "slide-right-off 4s ease 0s forwards";
            header.style.animation = "slide-up-off 4s ease 0s forwards";

            setTimeout(() => {
                window.location.replace("/" + resp.redirect);
            }, 4000);
        }
    } catch (Error) {
        console.log("error in log out" + Error);
    }
}
async function search(key) {
    try {
        // Don't call get_all here - it loads products unnecessarily
        // Just show search results
        const box = document.getElementById("search-show-content");
        const main = document.getElementById("search");
        const main_all = document.getElementById("main_view_all");
        main_all.style.pointerEvents = "none";
        main.style.pointerEvents = "all";
        main.style.backgroundColor = "rgba(10,10,10,0.3)";
        box.innerHTML = "";
        resp = { Key: key };
        const response = await fetch("http://localhost:80/search", {
            method: "post",
            headers: { "content-type": "application/json" },
            body: JSON.stringify(resp),
        });
        if (!response.ok) {
            throw new Error("failed to fetch");
        }
        let data = await response.json();
        if (data.success && data.content) {
            box.innerHTML = data.content;
            box.style.display = "flex";
        } else {
            box.innerHTML = "<p>No Match</p>";
        }
    } catch (Error) {
        console.log("error in search: " + Error);
    }
}
async function go_to_cart(ID, name, fromRestore = false) {
    close_side(true);

    if (!fromRestore) {
        update_history(
            { page: "cart", orderId: ID },
            "Shopping Cart",
            `/home/cart/${name || ID}`,
        );
    }

    // getting products by partners so checkout is on a single partner order
    try {
        //hide_cat_side(true);
        best_show(true);
        const main = document.getElementById("main-show");
        main.classList.add("container");
        const response = await fetch("http://localhost:80/orders/partner", {
            method: "POST",
            headers: { "content-type": "application/json" },
            body: JSON.stringify({ id: ID }),
        });
        if (!response.ok) {
            throw new Error("failed to fetch");
        }
        const resp = await response.json();
        if (resp.success && resp.content) {
            main.innerHTML = "";
            main.innerHTML = resp.content;
        }
    } catch (Error) {
        console.log("Error in go_to_cart:", Error);
    }
}
async function get_prod(name, pref, fromRestore = false) {
    hide_search(true);

    if (!fromRestore) {
        update_history(
            {
                page: "search-result",
                query: name,
                type: pref,
            },
            "Search Results",
            `/home/search/${pref}/${encodeURIComponent(name)}`,
        );
    }

    try {
        let data = { name: name, pref: pref };
        const main_all = document.getElementById("main_view_all");
        main_all.style.pointerEvents = "all";
        const main = document.getElementById("main-show");
        const search = document.getElementById("search-input");
        const response = await fetch("http://localhost:80/search/retrive", {
            method: "POST",
            headers: { "content-type": "application/json" },
            body: JSON.stringify(data),
        });
        if (!response.ok) {
            throw new Error("failed to fetch");
        }
        const resp = await response.json();
        if (resp.success && resp.content) {
            best_show(true);
            main.innerHTML = resp.content;

            if (search) {
                search.value = decodeURIComponent(name);
            }
        }
    } catch (Error) {
        console.log("error in get_prod:", Error);
    }
}

function payment(order_id) {
    try {
        document.cookie = "order_id=" + order_id;
        update_history(
            { page: "payment", orderId: order_id },
            "Payment",
            "/payment",
        );
        window.location.href = "/payment";
    } catch (Error) {
        console.log("error in payments" + Error);
    }
}
// error debug

// event pushstate

window.addEventListener("popstate", function (e) {
    console.log("POPSTATE EVENT:", e.state);

    // Set flag to prevent adding history during restore
    isrestoring = true;

    if (e.state) {
        console.log("Has state, restoring:", e.state.page);
        if (e.state.page === "home") {
            get_all(true);
        } else if (e.state.page === "products") {
            loadProducts(e.state.category, e.state.action, true); // Pass true for restore
        } else if (e.state.page === "cart" && e.state.orderId) {
            go_to_cart(e.state.orderId, true);
        } else if (e.state.page === "search-result" && e.state.query) {
            get_prod(e.state.query, e.state.type, true);
        } else {
            get_all(true);
        }
    } else {
        // No state - restore from URL with /home prefix
        const path = window.location.pathname;
        console.log("No state in popstate, restoring from URL:", path);

        let match = path.match(/\/home\/category\/(\d+)/);
        if (match) {
            loadProducts(match[1], "cat", true);
            return;
        }

        match = path.match(/\/home\/partner\/(\d+)/);
        if (match) {
            loadProducts(match[1], "part", true);
            return;
        }

        match = path.match(/\/home\/cart\/(.+)/);
        if (match) {
            best_show(true);
            go_to_cart(match[1], true);
            return;
        }

        best_show(false);
        get_all(true);
    }

    // Reset flag after the restore is complete
    setTimeout(() => {
        isrestoring = false;
        console.log("Restore complete, history updates re-enabled");
    }, 100);
});

//event listener for mouseout

// eventlistner for mouseover

// event listener for click
document.addEventListener("click", function (e) {
    if (e.target.id === "search-input") {
        e.target.style.backgroundColor = "white";
        e.target.style.color = "black";
        const search = document.getElementById("search");
        const search_box = document.getElementById("search-show-content");
        const main = document.getElementById("main_view_all");
        search.style.display = "flex";
        search.style.backgroundColor = "rgba(10,10,10,0.3)";
        search.style.zIndex = "1000";
        main.style.zIndex = "-2";
        search.removeEventListener("click", searchHandler);
        search.addEventListener("click", searchHandler);
    }
    if (e.target.id === "menu") {
        const side = document.getElementById("side_menu");
        e.target.classList.toggle("active");
        side.classList.toggle("show_cat");
    }
    if (e.target.id === "login-btn") {
        e.preventDefault();
        const err = document.getElementById("pass_err");
        const form = document.getElementById("log-form");
        const username = document
            .getElementById("log-form")
            .querySelectorAll(".form-in")[0];
        const pass = document
            .getElementById("log-form")
            .querySelectorAll(".form-in")[1];
        form.validation = "off";
        err.innerHTML = "";

        login("customer", username.value, pass.value, err);
    }

    if (e.target.id === "register-button") {
        console.log("reg hit");
        data = {};
        const inputs = document.querySelectorAll("#register-f input");
        console.log("ins:: " + inputs.length);
        const err  = document.getElementById('c-f-main_err')
        inputs.forEach((input) => {
            if (
                input.classList.contains("invalid") ||
                input.value.length == 0
            ) {
                err.innerHTML = "please meet all requirements";
                err.classList.remove("hidden");
                err.classList.add("err");
            } else {
                err.innerHTML = "";
                err.classList.add("hidden");
                err.classList.remove("err");
                data[input.id] = input.value;
            }
        });
        register(data);
    }
    if (e.target.id === "close-side") {
        close_side(true);
    }

    if (e.target.id === "sign-in-btn") {
        console.log("clicked 123456");
        const box = document.getElementById("log-container");
        const box2 = document.getElementById("product-container");
        const box4 = document.querySelector("footer");
        box2.style.opacity = 0.3;
        box4.style.opacity = 0.3;
        (box4, (box2.style.pointerEvents = "none"));
        box.style.animation = "none";
        box.offsetHeight;
        box.style.display = "flex";
        box.style.animation = "shrink 1s linear 0s 1 reverse forwards";
        console.log("done");
        box.style.pointerEvents = "all";
        box.style.zIndex = "1000";
    }
    if (e.target.id === "forgot-pass") {
        document.cookie = "origin = customer";
        window.location.href = "/retrive";
    }
    if (e.target.id === "cat_btn") {
        const box = document.getElementById("cat_extra_box");
        const cats = document.getElementById("cats");
        const dogs = document.getElementById("pats");
        box.style.display = "block";
        cats.style.display = "flex";
        dogs.style.display = "none";
    }
    if (e.target.id === "pat_btn") {
        const box = document.getElementById("cat_extra_box");
        const cats = document.getElementById("cats");
        const dogs = document.getElementById("pats");
        box.style.display = "block";
        cats.style.display = "none";
        dogs.style.display = "flex";
    }

    /* if(remember){
            document.cookie = `username=${username.value}; max-age=604800; path=/`;
            document.cookie = `password=${pass.value}; max-age=604800; path=/`;
        }*/
});

// input

document.addEventListener("input", function (e) {
    if (
        e.target.id === "c-f-name" ||
        e.target.id === "c-f-surname" ||
        e.target.id === "customer_fname"
    ) {
        const name = e.target.value;
        const err = document.getElementById("c-f-name_err");
        if (name.length <= 0 || /[!@#$%^&*]/.test(name)) {
            e.target.classList.add("invalid");
            err.innerHTML = "please enter a valid name";
            err.classList.remove("hidden");
        } else {
            e.target.classList.remove("invalid");
            err.innerHTML = "";
            err.classList.add("hidden");
        }
    }
    if (e.target.id === "c-f-email" || e.target.id === "customer_email") {
        const err = document.getElementById("c-f-email_err");
        const email = e.target.value;
        let isvalid = email.includes("@") && email.includes(".com");
        if (!isvalid) {
            err.classList.remove("hiddene");
            err.innerHTML = "please enter a valid email";
            e.target.classList.add("invalid");
        } else {
            err.classList.add("hiddene");
            err.innerHTML = "";
            e.target.classList.remove("invalid");
        }
    }
    if (e.target.id === "c-f-pass" || e.target.id === "customer_pass") {
        const pass = e.target.value;
        const err = document.getElementById("c-f-pass_err");
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
    if (e.target.id === "c-f-c-pass") {
        const c_pass = e.target.value;
        let err = document.getElementById("c-f-c-pass_err");
        const pass = document.getElementById("c-f-pass").value;
        if (pass === c_pass) {
            err.innerHTML = "";
            e.target.classList.remove("invalid");
            err.classList.add("hidden");
        } else {
            err.innerHTML = "Password must the same";
            e.target.classList.add("invalid");
            err.classList.remove("hidden");
        }
    }
    if (e.target.id === "c-f-phone" || e.target.id === "customer_tel") {
        tel = e.target;
        let haschar =
            /[!@#$%^&*]/.test(tel.value) ||
            /[A-Z]/.test(tel.value) ||
            /[a-z]/.test(tel.value);
        let err = document.getElementById("c-f-phone_err");
        let err2 = document.getElementById("profile_err");
        if (tel.value.length != 10) {
            tel.classList.add("invalid");
            err.innerHTML = "tel-number must be ";
            err.classList.remove("hidden");
        } else {
            if (haschar) {
                tel.classList.add("invalid");
                err.innerHTML =
                    "tel-number must not contain letters or special chars";
                err2.innerHTML =
                    "tel-number must not contain letters or special chars";
                err.classList.remove("hidden");
                err2.style.display = "inline";
            } else {
                err.innerHTML = "";
                err.classList.add("hidden");
                tel.classList.remove("invalid");
            }
        }
    }
    if (e.target.id === "search-input") {
        const value = e.target.value;
        const btn = document.getElementById("search-btn");
        if (value.length > 1) {
            search(value);
            btn.style.display = "block";
            hide_search(false);
        } else {
            hide_search(true);
            btn.style.display = "none";
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
        } else {
            const next = current - 1;
            if (next >= 1) {
                const next_inp = document.getElementById("otp_inp" + next);
                next_inp.focus();
            }
        }
    }
});

// evet listner for page load
document.addEventListener("DOMContentLoaded", function () {
    const path = window.location.pathname;
    box_management();
    if (path === "/home" || path === "/") {
        loadHomeWithHistory(); // This adds history only once
    } else {
        restore_page_state(); // This restores from URL/saved state
    }
});

document.addEventListener("mouseover", function (e) {});

document.addEventListener("mouseout", function (e) {});

// ob submit

document.addEventListener("submit", function (e) {
    e.preventDefault();
    console.log(e.target);
});
// on error

document.addEventListener(
    "error",
    function (e) {
        const target = e.target;
        if (target.tagName === "IMG") {
            if (!target.hasAttribute("data-error-fixed")) {
                target.setAttribute("data-error-fixed", "true");
                target.src = "/page/image/mainp.jpg";
            } else {
                target.style.display = "none";
                console.warn("Fallback image also failed for:", target.src);
            }
            target.onerror = null;
        }
    },
    true,
);
