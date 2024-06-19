const mockWindow = {
    location: { href: '' },
    confirm: function(message) {
        console.log(message);
        return this.confirmationResult;
    },
    setConfirmationResult: function(result) {
        this.confirmationResult = result;
    }
};

function confirmDelete() {
    if (mockWindow.confirm("Are you sure you want to delete your account?")) {
        mockWindow.location.href = "delete.php";
    }
}

function runTests() {
    mockWindow.setConfirmationResult(true);
    confirmDelete();
    console.assert(mockWindow.location.href === "delete.php", "Expected redirect to delete.php");

    mockWindow.setConfirmationResult(false);
    let initialHref = mockWindow.location.href;
    confirmDelete();
    console.assert(mockWindow.location.href === initialHref, "Expected no change in href");

    let confirmCalled = false;
    let expectedMessage = "Are you sure you want to delete your account?";
    mockWindow.confirm = function(message) {
        confirmCalled = true;
        console.assert(message === expectedMessage, "Expected confirmation message to match");
        return true;
    };

    confirmDelete();
    console.assert(confirmCalled, "Expected confirm to be called with message");

    console.log("All tests passed.");
}

runTests();