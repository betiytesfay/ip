document.addEventListener('DOMContentLoaded', function() {
    const sections = document.querySelectorAll('.section');
    const navigationLinks = document.querySelectorAll('#menu a');
   
  
    
    const selectedSectionId = localStorage.getItem('selectedSectionId');
  
    // Function to handle section selection
    function selectSection(sectionId) {
      sections.forEach(function(section) {
        section.classList.remove('selected');
      });
      navigationLinks.forEach(function(link) {
        link.classList.remove('selected');
      });
  
      const selectedSection = document.getElementById(sectionId);
      if (selectedSection) {
        selectedSection.classList.add('selected');
        const selectedLink = document.querySelector(`#menu a[href="#${sectionId}"]`);
        if (selectedLink) {
          selectedLink.classList.add('selected');
        
          selectedLink.click(); 
        }
      }
  
      // Store the selected section in localStorage
      localStorage.setItem('selectedSectionId', sectionId);
    }
  
    // Attach click event listeners to navigation links
    navigationLinks.forEach(function(link) {
      link.addEventListener('click', function(event) {
        event.preventDefault();
        const sectionId = link.getAttribute('href').substring(1);
        selectSection(sectionId);
      });
    });
  

    if (selectedSectionId) {
      selectSection(selectedSectionId);
    }
  });

  
  const logoutButton = document.getElementById('logout_btn');
logoutButton.addEventListener('click', function() {

  localStorage.removeItem('selectedSectionId');

});
