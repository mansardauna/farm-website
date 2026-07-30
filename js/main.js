// Ankabit Farm Client JavaScript connected to single processor.php
document.addEventListener('DOMContentLoaded', () => {
  let currentCaptchaId = '';
  let activeLeadId = '';

  // DOM Elements
  const captchaQuestionEl = document.getElementById('captchaQuestion');
  const refreshCaptchaBtn = document.getElementById('refreshCaptchaBtn');
  const step1Form = document.getElementById('leadStep1Form');
  const step2Form = document.getElementById('leadStep2Form');
  const step1Pane = document.getElementById('step1Pane');
  const step2Pane = document.getElementById('step2Pane');
  const successPane = document.getElementById('successPane');
  const step1Indicator = document.getElementById('step1Indicator');
  const step2Indicator = document.getElementById('step2Indicator');
  const formAlert = document.getElementById('formAlert');
  const crateInput = document.getElementById('quantityCrates');
  const estEggsEl = document.getElementById('estEggs');
  const estWeightEl = document.getElementById('estWeight');
  const mobileMenuBtn = document.getElementById('mobileMenuBtn');
  const mobileMenu = document.getElementById('mobileMenu');

  // Destination State & LGA Elements
  const deliveryStateSelect = document.getElementById('deliveryState');
  const deliveryLGASelect = document.getElementById('deliveryLGA');

  // Initialize Nigeria States Dropdown dynamically
  if (deliveryStateSelect && typeof NIGERIA_STATES_AND_LGAS !== 'undefined') {
    deliveryStateSelect.innerHTML = '<option value="">-- Select Destination State --</option>';
    const stateNames = Object.keys(NIGERIA_STATES_AND_LGAS).sort();
    
    stateNames.forEach(state => {
      const option = document.createElement('option');
      option.value = state;
      option.textContent = state;
      if (state === 'Lagos') option.selected = true; // Default selection
      deliveryStateSelect.appendChild(option);
    });

    updateLGADropdown(deliveryStateSelect.value || 'Lagos');

    deliveryStateSelect.addEventListener('change', (e) => {
      updateLGADropdown(e.target.value);
    });
  }

  function updateLGADropdown(stateName) {
    if (!deliveryLGASelect) return;
    deliveryLGASelect.innerHTML = '<option value="">-- Select Local Government (LGA) --</option>';
    
    if (stateName && NIGERIA_STATES_AND_LGAS[stateName]) {
      const lgas = NIGERIA_STATES_AND_LGAS[stateName].sort();
      lgas.forEach(lga => {
        const option = document.createElement('option');
        option.value = lga;
        option.textContent = lga;
        deliveryLGASelect.appendChild(option);
      });
      deliveryLGASelect.disabled = false;
    } else {
      deliveryLGASelect.disabled = true;
    }
  }

  // Distribution Services Slider Buttons Below Cards
  const servicesSlider = document.getElementById('servicesSlider');
  const sliderPrevBtn = document.getElementById('servicesSliderPrev');
  const sliderNextBtn = document.getElementById('servicesSliderNext');

  if (servicesSlider && sliderPrevBtn && sliderNextBtn) {
    sliderPrevBtn.addEventListener('click', (e) => {
      e.preventDefault();
      const cardWidth = servicesSlider.firstElementChild ? servicesSlider.firstElementChild.offsetWidth + 24 : 300;
      servicesSlider.scrollBy({ left: -cardWidth, behavior: 'smooth' });
    });

    sliderNextBtn.addEventListener('click', (e) => {
      e.preventDefault();
      const cardWidth = servicesSlider.firstElementChild ? servicesSlider.firstElementChild.offsetWidth + 24 : 300;
      servicesSlider.scrollBy({ left: cardWidth, behavior: 'smooth' });
    });
  }

  // Modals for Privacy Policy & Terms
  const privacyModal = document.getElementById('privacyModal');
  const termsModal = document.getElementById('termsModal');
  const openPrivacyBtns = document.querySelectorAll('.open-privacy-modal');
  const openTermsBtns = document.querySelectorAll('.open-terms-modal');
  const closeModalBtns = document.querySelectorAll('.close-modal-btn');
  const modalBackdrops = document.querySelectorAll('.modal-backdrop');

  openPrivacyBtns.forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.preventDefault();
      if (privacyModal) privacyModal.classList.add('active');
    });
  });

  openTermsBtns.forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.preventDefault();
      if (termsModal) termsModal.classList.add('active');
    });
  });

  closeModalBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      if (privacyModal) privacyModal.classList.remove('active');
      if (termsModal) termsModal.classList.remove('active');
    });
  });

  modalBackdrops.forEach(backdrop => {
    backdrop.addEventListener('click', (e) => {
      if (e.target === backdrop) {
        backdrop.classList.remove('active');
      }
    });
  });

  // Mobile Menu Toggle
  if (mobileMenuBtn && mobileMenu) {
    mobileMenuBtn.addEventListener('click', () => {
      const isExpanded = mobileMenuBtn.getAttribute('aria-expanded') === 'true';
      mobileMenuBtn.setAttribute('aria-expanded', !isExpanded);
      mobileMenu.classList.toggle('hidden');
    });
  }

  // Fetch Captcha from single processor.php endpoint
  fetchCaptcha();

  if (refreshCaptchaBtn) {
    refreshCaptchaBtn.addEventListener('click', (e) => {
      e.preventDefault();
      fetchCaptcha();
    });
  }

  async function fetchCaptcha() {
    if (!captchaQuestionEl) return;
    captchaQuestionEl.textContent = 'Loading captcha...';
    try {
      const res = await fetch('/processor.php?action=captcha');
      const data = await res.json();
      if (data.captchaId) {
        currentCaptchaId = data.captchaId;
        captchaQuestionEl.textContent = data.question;
      }
    } catch (err) {
      console.error('Failed to load captcha:', err);
      captchaQuestionEl.textContent = 'Solve: 5 + 5 = ?';
    }
  }

  function showAlert(msg, isError = true) {
    if (!formAlert) return;
    formAlert.classList.remove('hidden', 'bg-red-100', 'border-red-500', 'text-red-800', 'bg-emerald-100', 'border-emerald-500', 'text-emerald-800');
    if (isError) {
      formAlert.classList.add('bg-red-100', 'border-red-500', 'text-red-800');
    } else {
      formAlert.classList.add('bg-emerald-100', 'border-emerald-500', 'text-emerald-800');
    }
    formAlert.textContent = msg;
    formAlert.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
  }

  function hideAlert() {
    if (formAlert) formAlert.classList.add('hidden');
  }

  // Crate Calculator logic
  if (crateInput) {
    crateInput.addEventListener('input', () => {
      const val = parseInt(crateInput.value, 10) || 0;
      if (estEggsEl) estEggsEl.textContent = (val * 30).toLocaleString() + ' eggs';
      if (estWeightEl) estWeightEl.textContent = '~' + (val * 2).toLocaleString() + ' kg';
    });
  }

  // Handle Step 1 Submission to processor.php?action=step1
  if (step1Form) {
    step1Form.addEventListener('submit', async (e) => {
      e.preventDefault();
      hideAlert();

      const fullName = document.getElementById('fullName').value;
      const email = document.getElementById('email').value;
      const phone = document.getElementById('phone').value;
      const captchaAnswer = document.getElementById('captchaAnswer').value;
      const submitBtn = document.getElementById('step1SubmitBtn');

      if (!fullName || !email || !phone || !captchaAnswer) {
        showAlert('Please fill in all fields including security captcha.');
        return;
      }

      submitBtn.disabled = true;
      const originalText = submitBtn.innerHTML;
      submitBtn.innerHTML = `<svg class="animate-spin h-5 w-5 mx-auto text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>`;

      try {
        const response = await fetch('/processor.php?action=step1', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            fullName,
            email,
            phone,
            captchaId: currentCaptchaId,
            captchaAnswer
          })
        });

        const result = await response.json();

        if (result.success) {
          activeLeadId = result.leadId;
          showAlert('Step 1 Saved! Contact info recorded. Now select quantity & location.', false);

          setTimeout(() => {
            hideAlert();
            step1Pane.classList.remove('active-pane');
            step1Pane.classList.add('hidden-pane');
            
            step2Pane.classList.remove('hidden-pane');
            step2Pane.classList.add('active-pane');

            if (step1Indicator && step2Indicator) {
              step1Indicator.classList.remove('bg-blue-600', 'text-white');
              step1Indicator.classList.add('bg-emerald-600', 'text-white');
              step1Indicator.innerHTML = '✓';

              step2Indicator.classList.remove('bg-slate-200', 'text-slate-600');
              step2Indicator.classList.add('bg-blue-600', 'text-white', 'font-bold');
            }

            if (crateInput) crateInput.focus();
          }, 800);
        } else {
          showAlert(result.message || 'Error processing request.');
          fetchCaptcha();
          document.getElementById('captchaAnswer').value = '';
        }
      } catch (err) {
        console.error('Step 1 submission error:', err);
        showAlert('Network connection error. Please try again.');
      } finally {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
      }
    });
  }

  // Handle Step 2 Submission to processor.php?action=step2
  if (step2Form) {
    step2Form.addEventListener('submit', async (e) => {
      e.preventDefault();
      hideAlert();

      const quantityCrates = document.getElementById('quantityCrates').value;
      const deliveryState = document.getElementById('deliveryState').value;
      const deliveryLGA = document.getElementById('deliveryLGA') ? document.getElementById('deliveryLGA').value : '';
      const urgency = document.getElementById('urgency') ? document.getElementById('urgency').value : '';
      const notes = document.getElementById('notes') ? document.getElementById('notes').value : '';
      const submitBtn = document.getElementById('step2SubmitBtn');

      const qty = parseInt(quantityCrates, 10);
      if (isNaN(qty) || qty < 100) {
        showAlert('Minimum order is 100 crates. Please enter 100 or more.');
        return;
      }

      if (!deliveryState) {
        showAlert('Please select your destination state in Nigeria.');
        return;
      }

      submitBtn.disabled = true;
      const originalText = submitBtn.innerHTML;
      submitBtn.innerHTML = `<svg class="animate-spin h-5 w-5 mx-auto text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>`;

      try {
        const response = await fetch('/processor.php?action=step2', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            leadId: activeLeadId,
            quantityCrates: qty,
            deliveryState,
            deliveryLGA,
            urgency,
            notes
          })
        });

        const result = await response.json();

        if (result.success) {
          step2Pane.classList.remove('active-pane');
          step2Pane.classList.add('hidden-pane');

          if (successPane) {
            successPane.classList.remove('hidden-pane');
            successPane.classList.add('active-pane');
            document.getElementById('confLeadId').textContent = result.lead.id;
            document.getElementById('confQty').textContent = result.lead.quantityCrates + ' Crates (' + (result.lead.quantityCrates * 30).toLocaleString() + ' Eggs)';
            document.getElementById('confState').textContent = result.lead.deliveryState + (result.lead.deliveryLGA ? ' (' + result.lead.deliveryLGA + ' LGA)' : '');
          }
        } else {
          showAlert(result.message || 'Error saving order details.');
        }
      } catch (err) {
        console.error('Step 2 submission error:', err);
        showAlert('Network error. Please try submitting again.');
      } finally {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
      }
    });
  }
});
