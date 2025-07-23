const chatbotToggler = document.querySelector(".chatbot-toggler");
const closeBtn = document.querySelector(".close-btn");
const chatbox = document.querySelector(".chatbox");
const chatInput = document.querySelector(".chat-input textarea");
const sendChatBtn = document.querySelector(".chat-input span");
const userInput = document.getElementById('userMessage');
const sendButton = document.getElementById('send'); // Get the send button
const openChatButton = document.getElementById('open-chat'); // Get the chat open button
const inputBox = document.getElementById('input-box'); // Get the input box container


let userMessage = null; // Variable to store user's message
const inputInitHeight = chatInput.scrollHeight;

const createChatLi = (message, className) => {
    // Create a chat <li> element with passed message and className
    const chatLi = document.createElement("li");
    chatLi.classList.add("chat", `${className}`);
    let chatContent = className === "outgoing" ? `<p></p>` : `<span><i class='bx bxs-user-circle mt-2'></i></span><p></p>`;
    chatLi.innerHTML = chatContent;
    chatLi.querySelector("p").textContent = message;
    return chatLi; // return chat <li> element
}

const handleChat = () => {
    userMessage = chatInput.value.trim(); // Get user entered message and remove extra whitespace
    if(!userMessage) return;

    // Clear the input textarea and set its height to default
    chatInput.value = "";
    chatInput.style.height = `${inputInitHeight}px`;

    // Append the user's message to the chatbox
    chatbox.appendChild(createChatLi(userMessage, "outgoing"));
    chatbox.scrollTo(0, chatbox.scrollHeight);
}

chatInput.addEventListener("input", () => {
    // Adjust the height of the input textarea based on its content
    chatInput.style.height = `${inputInitHeight}px`;
    chatInput.style.height = `${chatInput.scrollHeight}px`;
});

chatInput.addEventListener("keydown", (e) => {
    // If Enter key is pressed without Shift key and the window 
    // width is greater than 800px, handle the chat
    if(e.key === "Enter" && !e.shiftKey && window.innerWidth > 800) {
        e.preventDefault();
        handleChat();
    }
});

sendChatBtn.addEventListener("click", handleChat);
closeBtn.addEventListener("click", () => document.body.classList.remove("show-chatbot"));
chatbotToggler.addEventListener("click", () => document.body.classList.toggle("show-chatbot"));

//Mga Code ni Nuels
// Selecting elements from the HTML document
//const chatbox = document.querySelector('.chatbox'); // Get the chatbox element

// Handle send button click
// sendButton.addEventListener('click', () => {
//     const message = userInput.value;
//     if (message.trim() !== '') {
//         sendMessage(message); // Send the user's message
//         displayMessage("User", message); // Display user's message in the chat
//         userInput.value = ''; // Clear the user input field
//         // Scroll to the latest message
//         chatbox.scrollTop = chatbox.scrollHeight;
//     }
// });

// // Handle Enter key press in the user input field
// userInput.addEventListener('keydown', (event) => {
//     if (event.key === 'Enter') {
//         const message = userInput.value;
//         if (message.trim() !== '') {
//             sendMessage(message); // Send the user's message
//             displayMessage("User", message); // Display user's message in the chat
//             userInput.value = ''; // Clear the user input field
//             // Scroll to the latest message
//             chatbox.scrollTop = chatbox.scrollHeight;
//         }
//     }
// });

// Function to send a message to the server
function sendMessage(message) {
    fetch('chatbot.php', {
        method: 'POST',
        body: new URLSearchParams({ message: message }),
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
    })
    .then(response => response.text())
    .then(data => {
        displayMessage("Chatbot", data); // Display the chatbot's response
        // Scroll to the latest message
        chatbox.scrollTop = chatbox.scrollHeight;
    })
    .catch(error => {
        console.error('Error:', error); // Handle errors
    });
}

// Function to display a message in the chat
function displayMessage(sender, message) {
    const messageDiv = document.createElement('div');
    messageDiv.innerHTML = message.replace(/\n/g, '<br>'); // Replace \n with <br>
    messageDiv.classList.add(sender === 'Chatbot' ? 'chatbot-response' : 'user-response'); // Add styling class
    chatbox.appendChild(messageDiv); // Append the message to the chatbox
    // Scroll to the latest message
    chatbox.scrollTop = chatbox.scrollHeight;
}
