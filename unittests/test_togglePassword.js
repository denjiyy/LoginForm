const mockDocument = {
    elements: {},
    getElementById: function(id) {
        return this.elements[id];
    },
    querySelector: function(selector) {
        if (selector === "button[type='button']") {
            return this.elements.button;
        }
    },
    createElement: function(tag) {
        return { type: "", id: "", textContent: "" };
    },
    setElement: function(id, element) {
        this.elements[id] = element;
    }
};

function simulateButtonClick() {
    var button = mockDocument.querySelector("button[type='button']");
    var clickEvent = new Event('click');
    button.dispatchEvent(clickEvent);
}

function togglePasswordVisibility() {
    var passwordField = mockDocument.getElementById("password");
    var button = mockDocument.querySelector("button[type='button']");

    if (passwordField.type === "password") {
        passwordField.type = "text";
        button.textContent = "Hide Password";
    } else {
        passwordField.type = "password";
        button.textContent = "Show Password";
    }
}

function testTogglePasswordVisibility() {
    const passwordField = { type: "password", id: "password" };
    const button = { type: "button", textContent: "Show Password", dispatchEvent: function() { togglePasswordVisibility(); } };

    mockDocument.setElement("password", passwordField);
    mockDocument.setElement("button", button);

    togglePasswordVisibility();
    console.assert(passwordField.type === "text", `Expected password field type to be "text", got "${passwordField.type}"`);
    console.assert(button.textContent === "Hide Password", `Expected button text content to be "Hide Password", got "${button.textContent}"`);

    togglePasswordVisibility();
    console.assert(passwordField.type === "password", `Expected password field type to be "password", got "${passwordField.type}"`);
    console.assert(button.textContent === "Show Password", `Expected button text content to be "Show Password", got "${button.textContent}"`);

    console.log("All tests passed.");
}

testTogglePasswordVisibility();