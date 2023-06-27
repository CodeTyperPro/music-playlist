const table = document.getElementById('table_search');
const tbody = table.querySelector('tbody');
const searchInput = document.getElementById('search_input');
var selectElement = document.getElementById("selectPlaylist");
var destElement = document.querySelectorAll('input[id=selectedId]');

async function refresh() {
  console.log(searchInput.value);
  console.log(destElement);

  const response = await fetch(`table.php?search=${searchInput.value}`);
  const data = await response.json();

  // Clear the table body
  tbody.innerHTML = '';

  // Loop through the data and add rows to the table
  data.forEach((d, index) => {
    const row = document.createElement('tr');
    row.innerHTML = `
      <th scope="row">${index + 1}</th>
      <td>${d.title}</td>
      <td>${d.artist}</td>
      <td>${d.length}</td>
      <td>${d.year}</td>
      <td>${d.genres}</td>
      <td>
        <form method="POST" action="add_to_playlist.php" novalidate>
          <div class="btn-group">
            <button type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">Action</button>
            <ul class="dropdown-menu">
              <input type="hidden" id="selectedId" name="playlist_id" value="-1">
              <input type="hidden" name="track_id" value="${d.id}">
              <input type="hidden" name="current_playlist_id" value="${selectElement.options[selectElement.selectedIndex].value}">
              <li>
                <button type="submit" class="dropdown-item btn btn-light">Add to my playlist</button>
              </li>
              <li>
                <hr class="dropdown-divider">
              </li>
              <li><a class="dropdown-item" href="#">Separated link</a></li>
            </ul>
          </div>
        </form>
      </td>
    `;
    tbody.appendChild(row);
  });


  destElement = document.querySelectorAll('input[id=selectedId]');
}

destElement.forEach(function(input) {
  input.value = selectElement.options[selectElement.selectedIndex].value;
});

selectElement.addEventListener("change", function() {
  var destElement = document.querySelectorAll('input[id=selectedId]');

  var selectedOption = selectElement.options[selectElement.selectedIndex];
  var selectedValue = selectedOption.value;
  var selectedText = selectedOption.text;

  // Perform desired actions with the selected value or text
  console.log("Selected value: " + selectedValue);
  console.log("Selected text: " + selectedText);

  destElement.forEach(function(input) {
      input.value = selectedValue;
  });

  console.log(destElement);

});


refresh();
searchInput.addEventListener('input', refresh);
