function togglePasswordVisibilityEdit() {
    var passwordField = document.getElementById("new_password");
    var button = document.querySelector("button[type='button']");

    if (passwordField.type === "password") {
        passwordField.type = "text";
        button.textContent = "Hide Password";
    } else {
        passwordField.type = "password";
        button.textContent = "Show Password";
    }
}