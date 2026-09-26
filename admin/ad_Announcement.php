<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Entity Management</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
<style>
    /* টেবিলের জন্য সাধারণ স্টাইলিং */
table {
  width: 100%;
  border-collapse: collapse;
  box-shadow: 0 4px 8px rgba(0,0,0,0.1);
  margin-top: 20px;
  border-radius: 8px;
  overflow: hidden;
}

/* হেডার সারির জন্য স্টাইল */
thead tr {
  background-color: #007bff; /* Bootstrap blue */
  color: #fff;
  font-weight: bold;
}

/* প্রতিটি টেবিল সেল এর জন্য স্টাইল */
th, td {
  padding: 12px 15px;
  border: 1px solid #dee2e6;
  text-align: left;
}

/* লিঙ্কের জন্য স্টাইল */
a {
  color: #0d6efd;
  text-decoration: none;
  font-weight: 500;
}

a:hover {
  text-decoration: underline;
  color: #0b5ed7;
}

/* বোতামের জন্য স্টাইল */
button {
  padding: 6px 12px;
  font-size: 14px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

button:hover {
  opacity: 0.9;
}


button:nth-child(1) {
  background-color: #ffc107;
  color: #fff;
}

button:nth-child(2) {
  background-color: #dc3545; 
  color: #fff;
}
#editModal {
  width: 400px;
  max-width: 90%;
  box-shadow: 0 4px 12px rgba(0,0,0,0.2);
  border-radius: 8px;
  padding: 20px;
  background-color: #fff;
}
  table {
    width: 100%;
    border-collapse: collapse;
  }
  th, td {
    border: 1px solid #ccc;
    padding: 8px;
    text-align: left;
  }
  button {
    margin-right: 5px;
  }
</style>
</head>
<body>

<h1 align="center"  txt color="blue">Entity Management</h1>

<table>
  <thead>
    <tr>
      <th>ID</th>
      <th>Title</th>
      <th>Message</th>
      <th>PDF</th>
      <th>Type</th>
      <th>Link</th>
      <th>Start Date</th>
      <th>End Date</th>
      <th>Actions</th>
    </tr>
  </thead>
  <tbody id="entityTableBody">
    <!-- Data rows will be inserted here dynamically -->
  </tbody>
</table>

<!-- Edit Modal -->
<div id="editModal" style="display:none; position:fixed; top:20%; left:30%; background:#fff; padding:20px; border:1px solid #ccc;">
  <h2>Edit Entity</h2>
  <form id="editForm">
    <input type="hidden" id="editId" />
    <label>Title:</label><br/>
    <input type="text" id="editTitle" /><br/><br/>
    <label>Message:</label><br/>
    <textarea id="editMessage"></textarea><br/><br/>
    <label>PDF Link:</label><br/>
    <input type="text" id="editPDF" /><br/><br/>
    <label>Type:</label><br/>
    <input type="text" id="editType" /><br/><br/>
    <label>Link:</label><br/>
    <input type="text" id="editLink" /><br/><br/>
    <label>Start Date:</label><br/>
    <input type="date" id="editStartDate" /><br/><br/>
    <label>End Date:</label><br/>
    <input type="date" id="editEndDate" /><br/><br/>
    <button type="button" onclick="saveEdit()">Save</button>
    <button type="button" onclick="closeEditModal()">Cancel</button>
  </form>
</div>

<script>
  // Sample Data (normally fetched from server)
  let entities = [
    {
      id: 1,
      title: "New Destination Added – Nikli Haor",
      message: "We are excited to announce that Nikli Haor is now ...",
      pdf: "uploads/announcements/nikli_haor_announcement.pdf",
      type: "destination",
      link: "destination_details.php?id=120",
      start_date: "2025-10-01T09:00",
      end_date: "2025-10-15T23:59:59"
    },
    {
      id: 2,
      title: "Hotel Promotion – Green View Resort",
      message: "Enjoy a 20% discount on Green View Resort for all ...",
      pdf: "uploads/announcements/green_view_offer.pdf",
      type: "hotel",
      link: "hotel_details.php?id=220",
      start_date: "2025-10-05T10:00",
      end_date: "2025-10-20T23:59:59"
    }
    // আরও এন্টিটি যোগ করতে পারেন
  ];

  function renderTable() {
    const tbody = document.getElementById('entityTableBody');
    tbody.innerHTML = "";
    entities.forEach(entity => {
      const row = document.createElement('tr');

      row.innerHTML = `
        <td>${entity.id}</td>
        <td>${entity.title}</td>
        <td>${entity.message}</td>
        <td><a href="${entity.pdf}" target="_blank">PDF</a></td>
        <td>${entity.type}</td>
        <td><a href="${entity.link}" target="_blank">Link</a></td>
        <td>${entity.start_date}</td>
        <td>${entity.end_date}</td>
        <td>
          <button onclick="editEntity(${entity.id})">Edit</button>
          <button onclick="deleteEntity(${entity.id})">Delete</button>
        </td>
      `;
      tbody.appendChild(row);
    });
  }

  function editEntity(id) {
    const entity = entities.find(e => e.id === id);
    if (entity) {
      document.getElementById('editId').value = entity.id;
      document.getElementById('editTitle').value = entity.title;
      document.getElementById('editMessage').value = entity.message;
      document.getElementById('editPDF').value = entity.pdf;
      document.getElementById('editType').value = entity.type;
      document.getElementById('editLink').value = entity.link;
      document.getElementById('editStartDate').value = entity.start_date.split('T')[0];
      document.getElementById('editEndDate').value = entity.end_date.split('T')[0];

      document.getElementById('editModal').style.display = 'block';
    }
  }

  function closeEditModal() {
    document.getElementById('editModal').style.display = 'none';
  }

  function saveEdit() {
    const id = parseInt(document.getElementById('editId').value);
    const entityIndex = entities.findIndex(e => e.id === id);
    if (entityIndex !== -1) {
      entities[entityIndex] = {
        id: id,
        title: document.getElementById('editTitle').value,
        message: document.getElementById('editMessage').value,
        pdf: document.getElementById('editPDF').value,
        type: document.getElementById('editType').value,
        link: document.getElementById('editLink').value,
        start_date: document.getElementById('editStartDate').value + "T00:00",
        end_date: document.getElementById('editEndDate').value + "T23:59:59"
      };
      renderTable();
      closeEditModal();
    }
  }

  function deleteEntity(id) {
    if (confirm("Are you sure you want to delete this entity?")) {
      entities = entities.filter(e => e.id !== id);
      renderTable();
    }
  }

  // Initial render
  renderTable();
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>
</html>