// ===== The "Are you sure?" box =====

var dialog = document.getElementById("confirm");
var whenYes = null;

function ask(text, yesWord, action, danger) {
    var yes = document.getElementById("confirm-yes");
    document.getElementById("confirm-text").textContent = text;
    yes.textContent = yesWord || "Yes";
    yes.className = danger ? "btn btn-danger" : "btn btn-primary";
    whenYes = action;
    dialog.showModal();
}

document.getElementById("confirm-no").addEventListener("click", function () {
    dialog.close();
});

document.getElementById("confirm-yes").addEventListener("click", function () {
    dialog.close();
    whenYes();
});

// Buttons with data-confirm ask first. Saying yes presses the button again, this time for real.
document.querySelectorAll("[data-confirm]").forEach(function (button) {
    button.addEventListener("click", function (event) {
        // Opening a round with an empty question would fail, so don't ask: the page will show what to fix
        var blank = Array.from(button.form.querySelectorAll(".question-row textarea")).some(function (box) {
            return box.value.trim().length < 5;
        });
        if (button.dataset.sure || !button.form.checkValidity() || (button.value === "open" && blank)) {
            return;
        }
        event.preventDefault();
        ask(button.dataset.confirm, button.dataset.yes, function () {
            button.dataset.sure = "yes";
            button.click();
        }, button.classList.contains("btn-danger"));
    });
});

// ===== Errors =====

// After a form comes back with an error, scroll to it. A marked box comes first, as it shows what to fix.
var firstError = document.querySelector(".missing") || document.querySelector("p.error:not(:empty), .panel.danger");
if (firstError) {
    firstError.scrollIntoView({ block: "center" });
}

// A red error goes away as soon as you change the field it is about
document.addEventListener("input", function (event) {
    var box = event.target.closest("fieldset, .question-row") || event.target.closest("label");
    if (!box) {
        return;
    }
    var error = box.querySelector(".error") || box.nextElementSibling;
    if (error && error.classList.contains("error")) {
        error.textContent = "";
    }
    box.classList.remove("missing");
});

// ===== Small helpers =====

// Live letter count under text boxes that have data-count
document.querySelectorAll("[data-count]").forEach(function (box) {
    var count = document.createElement("div");
    count.className = "small muted right";
    box.after(count);
    function update() {
        count.textContent = box.value.length + " / 1,000 letters";
    }
    box.addEventListener("input", update);
    update();
});

// Copy the join code. Browsers only allow this on localhost or https, so otherwise the button goes away.
document.querySelectorAll("[data-copy]").forEach(function (button) {
    button.hidden = !navigator.clipboard;
    button.addEventListener("click", function () {
        navigator.clipboard.writeText(button.dataset.copy);
        button.textContent = "Copied";
    });
});

// Stop a form being sent twice by a double click
document.querySelectorAll("form").forEach(function (form) {
    form.addEventListener("submit", function () {
        setTimeout(function () {
            form.querySelectorAll("button").forEach(function (button) {
                button.disabled = true;
            });
        }, 0);
    });
});
