function fetchWeather(lat, lon) {
const apiKey = "846965112d61ecee5abdc3f8255c3893"; 
const url = `https://api.openweathermap.org/data/2.5/weather?lat=${lat}&lon=${lon}&units=metric&appid=${apiKey}`
fetch(url)
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        document.getElementById("weather").innerHTML = 
            `${data.weather[0].main}, ${data.main.temp}°C`;
    })
    .catch(error => {
        console.error("Weather fetch error:", error);
        document.getElementById("weather").innerHTML = "⚠️ Failed to load weather.";
    });
}

document.addEventListener("DOMContentLoaded", () => {
if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(async position => {
        const lat = position.coords.latitude;
        const lon = position.coords.longitude;
        
        try {
            const response = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lon}`);
            const data = await response.json();
            const locationName = data.address.city || data.address.town || data.address.village || "Unknown location";
            document.getElementById("location").innerText = locationName;
            fetchWeather(lat, lon);
        } catch (error) {
            document.getElementById("location").innerText = "Location unavailable";
        }
    }, () => {
        document.getElementById("location").innerText = "Permission denied";
        document.getElementById("weather").innerText = "⚠️ Cannot fetch weather without location access.";
});

} else {
    document.getElementById("location").innerText = "Geolocation not supported";
    document.getElementById("weather").innerText = "⚠️ Weather unavailable.";
}
});

function calculateSavings() {
    const frequency = document.getElementById("watering-frequency").value;
    let savings = 0;
    if (frequency === "daily") savings = 50;
    else if (frequency === "every-2-days") savings = 30;
    else if (frequency === "weekly") savings = 15;
    document.getElementById("savings-result").innerText = `You can save up to ${savings}% on water usage!`;
}


const videos = {
    automated: 'field4.mp4',
    weather: 'field3.mp4',
    remote: 'sprinkler2.mp4'
};

function showVideo(feature) {
    const video = document.getElementById('featureVideo');
    const source = document.getElementById('videoSource');

    if (source.src.includes(videos[feature])) return;

    source.src = videos[feature];
    video.load();
    video.play();
}

document.addEventListener("DOMContentLoaded", function () {
    const features = document.querySelectorAll(".feature");

    features.forEach(feature => {
        feature.addEventListener("click", function () {
            features.forEach(f => f.classList.remove("selected"));

            this.classList.add("selected");

            const featureKey = this.getAttribute('onclick').match(/'(.*?)'/)[1];
            showVideo(featureKey);
        });
    });

    const firstFeature = features[0];
    firstFeature.classList.add("selected");
    showVideo('automated');
});


  window.addEventListener('scroll', () => {
    const navbar = document.getElementById('navbar');
    if (window.scrollY > 250) {
      navbar.classList.add('backdrop-blur-md', 'bg-black/30');
    } else {
      navbar.classList.remove('backdrop-blur-md', 'bg-black/30');
    }
  });

function showPlantTab(tabId) {
  const contents = document.querySelectorAll('.plant-content');
  contents.forEach(content => content.classList.add('hidden'));

  const selectedContent = document.getElementById(`${tabId}-content`);
  if (selectedContent) {
    selectedContent.classList.remove('hidden');
  }

  const tabButtons = document.querySelectorAll('.plant-tab-btn');
  tabButtons.forEach(btn => btn.classList.remove('selected'));

  const clickedButton = Array.from(tabButtons).find(btn =>
    btn.getAttribute('onclick')?.includes(tabId)
  );
  if (clickedButton) {
    clickedButton.classList.add('selected');
  }
}

function switchCaseStudy(index) {
  const cases = document.querySelectorAll('.case-study');
  const dots = document.querySelectorAll('.dot');
  cases.forEach((c, i) => c.style.display = i === index ? 'block' : 'none');
  dots.forEach((d, i) => d.classList.toggle('active', i === index));
  currentIndex = index; 
}

let currentIndex = 0;
let autoSwitchInterval;

function startAutoSwitch() {
  autoSwitchInterval = setInterval(() => {
    currentIndex = (currentIndex + 1) % document.querySelectorAll('.case-study').length;
    switchCaseStudy(currentIndex);
  }, 2500); 
}

window.addEventListener('DOMContentLoaded', () => {
  switchCaseStudy(0);
  startAutoSwitch();
});

document.addEventListener("DOMContentLoaded", () => {
  const chatbotBtn = document.getElementById("chatbot-toggle");
  const chatbotFrame = document.getElementById("chatbot-frame");
  const chatbotOverlay = document.getElementById("chatbot-overlay");

  chatbotBtn.addEventListener("click", () => {
    const isOpen = chatbotFrame.classList.contains("show");
    chatbotFrame.classList.toggle("show");
    chatbotOverlay.classList.toggle("show");
    chatbotBtn.classList.toggle("active", !isOpen);
  });

  chatbotOverlay.addEventListener("click", () => {
    chatbotFrame.classList.remove("show");
    chatbotOverlay.classList.remove("show");
    chatbotBtn.classList.remove("active");
  });
});

window.addEventListener("message", function (event) {
  if (event.data.action === "closeChatbot") {
    document.getElementById("chatbot-frame").classList.remove("show");
    document.getElementById("chatbot-overlay").classList.remove("show");
    document.getElementById("chatbot-toggle").classList.remove("active");
  }
});



