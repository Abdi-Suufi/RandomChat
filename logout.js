document.getElementById("logout-btn")?.addEventListener("click", function (e) {
    e.preventDefault(); // Prevent default link behavior
    
    // Clear localStorage
    localStorage.removeItem("username");
    localStorage.removeItem("user_id");
    localStorage.removeItem("display_name");
    localStorage.removeItem("is_admin");
    
    // Call the logout.php endpoint to destroy the PHP session
    fetch("logout.php")
        .then(() => {
            window.location.href = "index.html";
        })
        .catch(error => {
            console.error("Error during logout:", error);
            window.location.href = "index.html"; // Redirect anyway
        });
});