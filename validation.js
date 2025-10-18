
document.addEventListener("DOMContentLoaded", function () {
    const form = document.querySelector("form");
    const name = document.getElementById("name");
    const middleName = document.getElementById("middle name");
    const lastName = document.getElementById("last name");
    const email = document.getElementById("email");
    const phone = document.getElementById("phone");
    const password = document.getElementById("password");
    const confirmPassword = document.getElementById("conform password");

    const strengthText = document.createElement("small");
    strengthText.style.display = "block";
    password.insertAdjacentElement("afterend", strengthText);
    function checkPasswordStrength(pwd) {
        let strength = 0;
        if (pwd.length >= 8) strength++;
        if (/[A-Z]/.test(pwd)) strength++;
        if (/[a-z]/.test(pwd)) strength++;
        if (/[0-9]/.test(pwd)) strength++;
        if (/[@$!%*?&]/.test(pwd)) strength++;

        switch (strength) {
            case 0:
            case 1:
                return { text: "Very Weak", color: "red" };
            case 2:
                return { text: "Weak", color: "orange" };
            case 3:
                return { text: "Medium", color: "blue" };
            case 4:
                return { text: "Strong", color: "green" };
            case 5:
                return { text: "Very Strong", color: "darkgreen" };
        }
    }
    password.addEventListener("input", function () {
        const { text, color } = checkPasswordStrength(password.value);
        strengthText.textContent = `Strength: ${text}`;
        strengthText.style.color = color;
    });
    form.addEventListener("submit", function (e) {
        let errors = [];
        if (name.value.trim() === "") {
            errors.push("First name is required.");
        }
        if (middleName.value.trim() === "") {
            errors.push("Middle name is required.");
        }
        if (lastName.value.trim() === "") {
            errors.push("Last name is required.");
        }
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailPattern.test(email.value.trim())) {
            errors.push("Please enter a valid email address.");
        }
        const phonePattern = /^\d{10}$/;
        if (!phonePattern.test(phone.value.trim())) {
            errors.push("Phone number must be 10 digits.");
        }
        if (password.value.length < 8) {
            errors.push("Password must be at least 8 characters long.");
        }
        if (password.value !== confirmPassword.value) {
            errors.push("Passwords do not match.");
        }
        if (errors.length > 0) {
            e.preventDefault();
            alert(errors.join("\n"));
        }
    });
});
