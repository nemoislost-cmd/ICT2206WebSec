// decalring of global variable 
var clickedX;
var clickedY;
var circle;
var captchaStartTime;
var captchaImage = document.getElementById("captcha-image");
var captchaContainer = document.getElementById("captcha-container");
var captchaEndTime;

// When the page loads, set a flag in localStorage
window.addEventListener('load', function () {
    localStorage.setItem('tabIsOpen', true);
});

// When the user closes the page, remove the flag from localStorage
window.addEventListener('beforeunload', function () {
    localStorage.removeItem('tabIsOpen');
});

// Check if the flag exists in localStorage to detect if the same tab is open
if (localStorage.getItem('tabIsOpen')) {
    // The same tab is open
    alert("There is an existing tab opened!");
    window.close();
} else {
    // The tab is not open
    // Add an event listener to detect changes in localStorage
    window.addEventListener('storage', function (event) {
        if (event.key === 'tabIsOpen' && event.newValue === null) {
            // The localStorage flag has been removed, indicating that the tab has been closed
            alert("The new tab has been closed!");
            localStorage.setItem('tabIsOpen', true);
        }
    });
}

function validateForm() {
    clickedX = document.getElementById("clickedX").value;
    clickedY = document.getElementById("clickedY").value;
    if (clickedX === "" || clickedY === "") {
        alert("Please click on the image to select the location of the hidden object.");
        return false;
    }
    return true;
}

// Add click event listener to the image

captchaImage.addEventListener("click", function (event)
{
    var container = document.getElementById('captcha-container');
    var containerRect = container.getBoundingClientRect();
    var containerLeft = containerRect.left;
    var containerTop = containerRect.top;

    // Get the position of the click relative to the container
    clickedX = event.pageX - containerLeft;
    clickedY = event.pageY - containerTop;
    // Remove any existing circle
    var existingCircle = document.querySelector('.circle');
    if (existingCircle) {
        existingCircle.remove();
    }
    circle = createCircle(clickedX, clickedY);
    captchaContainer.appendChild(circle);

    document.getElementById("clickedX").value = clickedX;
    document.getElementById("clickedY").value = clickedY;

    captchaEndTime = new Date().getTime();
});

function complete_captcha() {
    var timeTaken = captchaEndTime - captchaStartTime;
//    console.log(timeTaken);
    document.getElementById("time_taken").value = timeTaken;
}

function createCircle(x, y) {
    circle = document.createElement("div");
    circle.setAttribute("class", "circle");
    circle.style.left = x - 30 + "px";
    circle.style.top = y - 30 + "px";

    // Add click event listener to the circle
    circle.addEventListener("click", function (event) {
        // Remove the current circle
        circle.remove();

        // Get the position of the click relative to the container
        var container = document.getElementById("captcha-container");
        var containerRect = container.getBoundingClientRect();
        var containerLeft = containerRect.left;
        var containerTop = containerRect.top;
        var clickedX = event.pageX - containerLeft;
        var clickedY = event.pageY - containerTop;

        // Add a new circle at the same position as the previous one
        circle = createCircle(clickedX, clickedY);
        captchaContainer.appendChild(circle);

        document.getElementById("clickedX").value = clickedX;
        document.getElementById("clickedY").value = clickedY;

        captchaEndTime = new Date().getTime();
    });

    return circle;
}

function show_image() {
    var show_image_btn = document.getElementById("btn_show_image");
    var hidden = captchaImage.getAttribute("hidden");

    if (hidden) {
        captchaImage.removeAttribute("hidden");
        show_image_btn.parentNode.removeChild(show_image_btn);
        captchaStartTime = new Date().getTime();
        return false;
    }
}