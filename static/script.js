function updateDropdownText(text) {
    document.getElementById('userTransactionsDropdown').innerText = text;
}

$(document).ready(function () {
    $(".user-option").click(function () {
        var selectedUsername = $(this).data("username");

        $.ajax({
            type: "POST",
            url: "viewUsersTransactions.php",
            data: { username: selectedUsername },
            dataType: "json", // Expect JSON response
            success: function (data) {
                // Update the table body with the returned data
                updateTable(data);
            },
            error: function (xhr, status, error) {
                console.error("Error:", error);
            }
        });
    });

    function updateTable(transactions) {
        // Update the table body with the received transactions
        // Assuming transactions is an array of objects with the same structure as your PHP script
        var tableBody = $("#tableBody");
        tableBody.empty();

        $.each(transactions, function (index, transaction) {
            var row = $("<tr>");
            row.append("<th scope='row'>" + transaction.username + "</th>");
            row.append("<td>" + transaction.email + "</td>");
            row.append("<td>" + transaction.account_number + "</td>");
            row.append("<td>" + transaction.type + "</td>");
            row.append("<td>" + transaction.amount + "</td>");
            row.append("<td>" + transaction.time + "</td>");
            row.append("<td>" + transaction.transaction_id + "</td>");

            tableBody.append(row);
        });
    }
});