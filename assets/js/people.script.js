// document.addEventListener("DOMContentLoaded", () => {
//   const navbar = document.querySelector(".navbar");
//   document.body.style.paddingTop = navbar.offsetHeight + "px";
// });

document.addEventListener('DOMContentLoaded', () => {
  // Select all profile cards
  const cards = document.querySelectorAll('.profile-card');

  cards.forEach((card, index) => {
    const desc = card.querySelector('.profileDesc');

    // --- Mouse Enter Event ---
    card.addEventListener('mouseenter', () => {
      // Show the description text of the hovered card
      if (desc) {
        desc.classList.remove('d-none');
      }

      // New logic to handle adjacent card height
      // First, check if the cards are using the h-100 utility
      if (card.classList.contains('h-100')) {
        let targetCard = null;

        // If the card is at an EVEN index (1st, 3rd, etc. position)
        if (index % 2 === 0) {
          // Target the card immediately AFTER it
          targetCard = cards[index + 1];
        } else { // If the card is at an ODD index (2nd, 4th, etc. position)
          // Target the card immediately BEFORE it
          targetCard = cards[index - 1];
        }

        // If the target card exists, remove its h-100 class
        // This allows the hovered card to expand without shifting other cards
        if (targetCard) {
          targetCard.classList.remove('h-100');
        }
      }
    });

    // --- Mouse Leave Event ---
    card.addEventListener('mouseleave', () => {
      // Hide the description text again
      if (desc) {
        desc.classList.add('d-none');
      }

      // New logic to restore adjacent card height
      if (card.classList.contains('h-100')) {
        let targetCard = null;

        // Use the same logic to find the adjacent card
        if (index % 2 === 0) {
          targetCard = cards[index + 1];
        } else {
          targetCard = cards[index - 1];
        }

        // If the target card exists, restore its h-100 class
        if (targetCard) {
          targetCard.classList.add('h-100');
        }
      }
    });
  });
});
