(function() {
  "use strict";
  var main = "";
  function countdown(element) {
    const configString = element.dataset.zionCountdownConfig || {};
    const config = JSON.parse(configString);
    const l10n = window.zbProCountdownData;
    let daysString = config.daysString || l10n.days;
    let hoursString = config.hoursString || l10n.hours;
    let minutesString = config.minutesString || l10n.minutes;
    let secondsString = config.secondsString || l10n.seconds;
    let countDownDate;
    daysString = daysString.split(",").length > 0 ? daysString.split(",")[0] : `${daysString}`;
    hoursString = hoursString.split(",").length > 0 ? hoursString.split(",")[0] : `${hoursString}`;
    minutesString = minutesString.split(",").length > 0 ? minutesString.split(",")[0] : `${minutesString}`;
    secondsString = secondsString.split(",").length > 0 ? secondsString.split(",")[0] : `${secondsString}`;
    if (config.finalDate) {
      countDownDate = new Date(config.finalDate).getTime();
    } else if (config.evergreen_config) {
      const { uid } = config.evergreen_config;
      if (!window.ZnPbInitialData && localStorage.getItem(`zb_countdown_timer_${uid}`)) {
        countDownDate = localStorage.getItem(`zb_countdown_timer_${uid}`);
      } else {
        countDownDate = getEverGreenDate();
        localStorage.setItem(`zb_countdown_timer_${uid}`, countDownDate);
      }
    }
    function getEverGreenDate() {
      const { days = 0, hours = 0, minutes = 0 } = config.evergreen_config;
      const evergreenDate = new Date();
      const evergreenHours = days * 24 + hours;
      const currentHours = evergreenDate.getHours();
      const currentMinutes = evergreenDate.getMinutes();
      evergreenDate.setHours(currentHours + evergreenHours, currentMinutes + minutes);
      return evergreenDate.getTime();
    }
    if (!countDownDate) {
      console.warn("Countdown doesn't have a date set.");
    }
    const wrapper = document.createElement("div");
    wrapper.classList.add("zb-el-countdown__wrapper");
    element.appendChild(wrapper);
    const countdownInterval = setInterval(run, 1e3);
    function run() {
      wrapper.innerHTML = getMarkup();
      checkExpirationAction();
    }
    function checkExpirationAction() {
      var now = new Date().getTime();
      const distance = countDownDate - now;
      if (distance < 0) {
        if (config.expirationAction === "hide" && !window.ZnPbInitialData) {
          element.style.display = "none";
        } else if (config.expirationAction === "message" && config.expirationMessage) {
          wrapper.innerHTML = config.expirationMessage;
        } else if (config.expirationAction === "redirect" && config.redirectURL && !window.ZnPbInitialData) {
          window.location = config.redirectURL;
        }
        if (config.expirationAction === "restart" && config.evergreen_config) {
          countDownDate = getEverGreenDate();
          const { uid } = config.evergreen_config;
          localStorage.removeItem(`zb_countdown_timer_${uid}`);
        } else {
          clearInterval(countdownInterval);
        }
      }
    }
    function getMarkup() {
      var now = new Date().getTime();
      const distance = countDownDate - now;
      const days = Math.floor(distance / (1e3 * 60 * 60 * 24));
      const hours = Math.floor(distance % (1e3 * 60 * 60 * 24) / (1e3 * 60 * 60));
      const minutes = Math.floor(distance % (1e3 * 60 * 60) / (1e3 * 60));
      const seconds = Math.floor(distance % (1e3 * 60) / 1e3);
      return getSingleString(days < 0 ? 0 : days, daysString) + getSingleString(hours < 0 ? 0 : hours, hoursString) + getSingleString(minutes < 0 ? 0 : minutes, minutesString) + getSingleString(seconds < 0 ? 0 : seconds, secondsString);
    }
    function getSingleString(val, unit) {
      return `<div class="zb-el-countdownUnit"><span class="zb-el-countdownUnit__value">${val}</span><span class="zb-el-countdownUnit__unit">${unit}</span></div>`;
    }
    function destroy() {
      element.removeChild(wrapper);
      clearInterval(countdownInterval);
    }
    run();
    return {
      destroy
    };
  }
  const elements = Array.from(document.querySelectorAll(".zb-el-countdown"));
  elements.forEach((element) => {
    countdown(element);
  });
  window.zbScripts = window.zbScripts || {};
  window.zbScripts.countdown = countdown;
})();
