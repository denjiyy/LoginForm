function togglePasswordVisibility() {
    var passwordField = document.getElementById("password");
    var button = document.querySelector("button[type='button']");

    if (passwordField.type === "password") {
        passwordField.type = "text";
        button.textContent = "Hide Password";
    } else {
        passwordField.type = "password";
        button.textContent = "Show Password";
    }
}