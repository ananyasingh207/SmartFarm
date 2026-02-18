const chatBody = document.querySelector(".chat-body");
const messageInput = document.querySelector(".message-input");
const sendMessageButton = document.querySelector("#send-message");

const API_URL = "chatbot_api.php";

const userData = { message: null };
const chatHistory = [];
const initialInputHeight = messageInput.scrollHeight;


// Create message element
const createMessageElement = (content, ...classes) => {
    const div = document.createElement("div");
    div.classList.add("message", ...classes);
    div.innerHTML = content;
    return div;
};


// Generate Agriculture Expert Response (Hindi + English Support)
const generateBotResponse = async (incomingMessageDiv) => {

    const messageElement = incomingMessageDiv.querySelector(".message-text");

    // Add user message to history
    chatHistory.push({
        role: "user",
        parts: [{ text: userData.message }]
    });

    const requestOptions = {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({

            systemInstruction: {
                role: "system",
                parts: [{
                    text: `
You are a Smart Irrigation and Agriculture Assistant helping Indian farmers.

LANGUAGE RULE:
- If user writes in Hindi → Reply in Hindi.
- If user writes in English → Reply in English.
- Keep language simple and easy to understand.

YOUR ROLE:
- Help with irrigation methods (drip, sprinkler, flood)
- Suggest crop management practices
- Recommend fertilizers and soil improvements
- Suggest pest control solutions
- Guide water-saving techniques
- Provide seasonal crop advice
- Help increase yield sustainably

STYLE:
- Give step-by-step practical advice
- Use short clear sentences
- Avoid technical jargon
- Stay strictly within agriculture topics
- If question is unrelated, politely redirect to farming

Be friendly and supportive like a farming expert.
`
                }]
            },

            contents: chatHistory
        })
    };

    try {

        const response = await fetch(API_URL, requestOptions);
        const data = await response.json();

        if (!response.ok) {
            throw new Error(data.error?.message || "API Error");
        }

        const apiResponseText =
            data.candidates?.[0]?.content?.parts?.[0]?.text?.trim() ||
            "माफ़ कीजिए, उत्तर उपलब्ध नहीं है।";

        messageElement.innerText = apiResponseText;

        // Save bot response correctly
        chatHistory.push({
            role: "model",
            parts: [{ text: apiResponseText }]
        });

    } catch (error) {

        console.error(error);
        messageElement.innerText = "Error: " + error.message;
        messageElement.style.color = "#ff0000";

    } finally {

        incomingMessageDiv.classList.remove("thinking");
        chatBody.scrollTo({
            top: chatBody.scrollHeight,
            behavior: "smooth"
        });
    }
};


// Handle outgoing message
const handleOutgoingMessage = (e) => {

    e.preventDefault();

    const message = messageInput.value.trim();
    if (!message) return;

    userData.message = message;

    messageInput.value = "";
    messageInput.dispatchEvent(new Event("input"));

    const messageContent = `<div class="message-text">${message}</div>`;
    const outgoingMessageDiv = createMessageElement(
        messageContent,
        "user-message"
    );

    chatBody.appendChild(outgoingMessageDiv);

    chatBody.scrollTo({
        top: chatBody.scrollHeight,
        behavior: "smooth"
    });

    setTimeout(() => {

        const botContent = `
        <div class="message-text">
            <div class="thinking-indicator">
                <div class="dot"></div>
                <div class="dot"></div>
                <div class="dot"></div>
            </div>
        </div>`;

        const incomingMessageDiv = createMessageElement(
            botContent,
            "bot-message",
            "thinking"
        );

        chatBody.appendChild(incomingMessageDiv);

        chatBody.scrollTo({
            top: chatBody.scrollHeight,
            behavior: "smooth"
        });

        generateBotResponse(incomingMessageDiv);

    }, 600);
};


// Enter key send
messageInput.addEventListener("keydown", (e) => {

    if (e.key === "Enter" && !e.shiftKey && window.innerWidth > 768) {
        handleOutgoingMessage(e);
    }

});


// Auto resize input
messageInput.addEventListener("input", () => {

    messageInput.style.height = `${initialInputHeight}px`;
    messageInput.style.height = `${messageInput.scrollHeight}px`;

    document.querySelector(".chat-form").style.borderRadius =
        messageInput.scrollHeight > initialInputHeight
            ? "15px"
            : "32px";
});


// Send button
sendMessageButton.addEventListener("click", handleOutgoingMessage);


// Close chatbot
document.getElementById("close-chatbot")?.addEventListener("click", function () {
    parent.postMessage({ action: "closeChatbot" }, "*");
});
