// Logout functionality
document.getElementById("logout-btn")?.addEventListener("click", function () {
    localStorage.removeItem("username");
    localStorage.removeItem("user_id");
    window.location.href = "index.html";
});