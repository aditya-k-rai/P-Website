const sheetId = '19WYmbUIuF_9FdhYA93ZLrNGHj3RlDE56pWsQQX05Qv0'; // Replace with your Google Sheet ID
const sheetName = 'Bookings';

function doPost(e) {
  // Get spreadsheet using the ID
  const sheet = SpreadsheetApp.openById(sheetId).getActiveSheet();
  
  // Parse the incoming data
  const data = JSON.parse(e.postData.contents);
  
  // Format data into a row
  const rowData = [
    new Date(), // Timestamp
    data.name,
    data.phone,
    data.date,
    data.persons,
    data.meal_preference,
    data.occasion,
    data.time,
    data.email,
    data.comments
  ];
  
  // Append the data as a new row
  sheet.appendRow(rowData);
  
  // Return success response
  return ContentService.createTextOutput('Success');
}
