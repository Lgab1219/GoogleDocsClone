
$("#registerForm").on('submit', function(event) {
    event.preventDefault();

    var formData = {
        registerEmailInput: $("#registerEmailInput").val(),
        registerUsernameInput: $("#registerUsernameInput").val(),
        registerPasswordInput: $("#registerPasswordInput").val(),
        registerRoleInput: $("#registerRoleInput").val(),
        suspendInput: $("#suspendInput").val(),
        registerAccount: 1
    };

    if (formData.registerEmailInput !== "" && formData.registerUsernameInput !== "" && 
        formData.registerPasswordInput !== "" && formData.registerRoleInput !== "" && formData.suspendInput !== "") {
        $.ajax({
            type: "POST",
            url: "core/controller.php",
            data: formData,
            dataType: "json",
            success: function(response) {
                if (response.status === "success") {
                    window.location.href = response.redirect;
                } else if (response.status === "error") {
                    $("#message").html(`<p style="color:red;">${response.message}</p>`);
                }
            },
            error: function() {
                $("#message").html(`<p style="color:red;">Something went wrong. Please try again.</p>`);
            }
        });
    } else {
        $("#message").html(`<p style="color:red;">Please fill out all input fields!</p>`);
    }
});

$("#loginForm").on('submit', function(event) {
    event.preventDefault();

    var formData = {
        loginEmailInput: $("#loginEmailInput").val(),
        loginPasswordInput: $("#loginPasswordInput").val(),
        loginAccount: 1
    };

    if(formData.loginEmailInput != "" && formData.loginPasswordInput != "") {
        $.ajax({
            type: "POST",
            url: "core/controller.php",
            data: formData,
            success: function(response) {
                if(response.status === "success"){
                    window.location.href = response.redirect;
                } else if (response.status === "error") {
                    $("#messageLogin").html(`<p style="color:red;">${response.message}</p>`);
                }
            },
            error: function() {
                $("#messageLogin").html(`<p style="color:red;">Something went wrong. Please try again.</p>`);
            }
        })
    } else {
        $("#messageLogin").html(`<p style="color:red;">Please fill out all input fields!</p>`);
    }
});

$("#createDocumentBtn").on("click", function (event) {
    event.preventDefault();

    $.ajax({
        type: "POST",
        url: "core/controller.php",
        data: {
            createDocument: 1
        },
        dataType: "json",
        success: function (response) {
            if (response.status === "success") {
                window.location.href = "editDocument.php?documentID=" + response.documentID;
            } else {
                alert("Error: " + response.message);
            }
        },
        error: function (xhr, status, error) {
            console.error("AJAX error:", status, error);
            console.log(xhr.responseText);
        }
    });
});

// This is for the manual saving event handler
$("#documentForm").on("submit", function (event) {
    event.preventDefault();

    const documentID = new URLSearchParams(window.location.search).get("documentID");
    const title = $("#documentTitleInput").val();
    const text = $("#documentTextInput").html();

    $.ajax({
        type: "POST",
        url: "core/controller.php",
        data: {
            updateDocument: 1,
            documentID: documentID,
            title: title,
            text: text
        },
        dataType: "json",
        success: function (response) {
            if (response.status === "success") {
                alert("Document saved successfully.");
            } else {
                alert("Error: " + response.message);
            }
        },
        error: function (xhr, status, error) {
            console.error("AJAX error:", status, error);
            console.log(xhr.responseText);
        }
    });
});

$(document).ready(function () {
    const documentID = new URLSearchParams(window.location.search).get("documentID");

    if (documentID) {
        $.ajax({
            type: "GET",
            url: "core/controller.php",
            data: {
                getDocument: 1,
                documentID: documentID
            },
            dataType: "json",
            success: function (response) {
                if (response.status === "success") {
                    $("#documentTitleInput").val(response.document.documentTitle);
                    $("#documentTextInput").html(response.document.documentText);
                    fetchLogs();
                } else {
                    alert("Failed to load document: " + response.message);
                    window.location.href = response.redirect;
                }
            },
            error: function (xhr, status, error) {
                console.error("Error loading document:", status, error);
            }
        });

        loadMessages();

        setInterval(loadMessages, 5000);
    }
});

// Autosave when user types in the title
$("#documentTitleInput").on("input", function () {
    clearTimeout(autosaveTimeout);
    autosaveTimeout = setTimeout(autosave, debounceDelay);
});

// Autosave when user types in the document content
$("#documentTextInput").on("input", function () {
    clearTimeout(autosaveTimeout);
    autosaveTimeout = setTimeout(autosave, debounceDelay);

    // Autosave doesn't seem to catch the changes when I change the format of the text unless I save it manually
    // but when I change the text itself, the autosave works. So I added this block of code to manually observe the format
    // change of the text every time this event handler runs.
    const observer = new MutationObserver(() => {
    clearTimeout(autosaveTimeout);
    autosaveTimeout = setTimeout(autosave, debounceDelay);
    });

    observer.observe(document.getElementById("documentTextInput"), {
    childList: true,
    subtree: true,
    characterData: true
});

});


// This is for the autosave feature
let autosaveTimeout;
const debounceDelay = 1000;


function autosave() {
    const title = $("#documentTitleInput").val();
    let documentText = $("#documentTextInput").html();
    const documentID = new URLSearchParams(window.location.search).get("documentID");

    console.log(documentText);  // Log the HTML to ensure changes are captured

    $.ajax({
        type: "POST",
        url: "core/controller.php",
        data: {
            updateDocument: 1,
            documentID: documentID,
            title: title,
            text: documentText
        },
        dataType: "json",
        success: function (response) {
            if (response.status === "success") {
                $("#message").html("<p>Document auto-saved.</p>");
                console.log("Document auto-saved.");
                fetchLogs();
            } else {
                console.warn("Autosave failed: " + response.message);
            }
        },
        error: function (status, error) {
            console.error("Autosave AJAX error:", status, error);
        }
    });
}

// This function changes the format of the text once a text has been highlighted/selected
function formatText(tag) {
    const selection = window.getSelection();
    if (!selection.rangeCount) return;

    const range = selection.getRangeAt(0);
    const selectedContent = range.extractContents();

    const wrapper = document.createElement(tag);
    wrapper.appendChild(selectedContent);

    range.insertNode(wrapper);

    // Move cursor after inserted element
    range.setStartAfter(wrapper);
    range.setEndAfter(wrapper);
    selection.removeAllRanges();
    selection.addRange(range);
}

$(document).on('change', '.account', function() {
    const username = $(this).val();
    const isSuspended = $(this).is(':checked') ? 1 : 0;

    $.ajax({
        type: "POST",
        url: "core/controller.php",
        data: {
            toggleSuspend: 1,
            username: username,
            suspend: isSuspended
        },
        dataType: "json",
        success: function(response) {
            if (response.status === "success") {
                alert("Suspend status updated.");
            } else {
                console.log("Error:", response.message);
            }
        },
        error: function() {
            console.log("AJAX error.");
        }
    });
});

$('#userSearch').on('keyup', function() {
    const query = $(this).val();
    if (query.length < 2) return;

    $.ajax({
        type: 'GET',
        url: 'core/controller.php',
        data: { searchUsers: 1, query: query },
        dataType: 'json',
        success: function(users) {
            $('#searchResults').empty();
            users.forEach(user => {
                $('#searchResults').append(`
                    <li>
                        ${user.username}
                        <button class="grantAccess" data-id="${user.accountID}">Grant Access</button>
                    </li>
                `);
            });
        }
    });
});

$(document).on('click', '.grantAccess', function() {
    const accountID = $(this).data('id');
    const documentID = $('#docID').val();

    $.ajax({
        type: 'POST',
        url: 'core/controller.php',
        data: {
            grantAccess: 1,
            accountID: accountID,
            documentID: documentID
        },
        dataType: 'json',
        success: function(response) {
            alert(response.status === 'success' ? 'Access granted!' : 'Error granting access.');
        }
    });
});

function fetchLogs() {
    const documentID = new URLSearchParams(window.location.search).get("documentID");

    $.ajax({
        type: 'GET',
        url: 'core/controller.php',
        data: { fetchLogs: 1, documentID: documentID },
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                const logs = response.logs;
                const logContainer = $('#logContainer');
                logContainer.empty();

                if (logs.length === 0) {
                    logContainer.html('<p>No logs found.</p>');
                    return;
                }

                logs.forEach(log => {
                    const actionLabel = {
                        edited_title: 'edited the title',
                        edited_text: 'edited the text',
                        created: 'created the document'
                    }[log.action] || log.action;

                    let html = `<div class="log-entry">
                        <strong>${log.username}</strong> ${actionLabel} on <em>${log.timestamp}</em><br>`;

                    if (log.action === 'edited_title') {
                        html += `Title: <strong>${log.oldValue}</strong> → <strong>${log.newValue}</strong>`;
                    } else if (log.action === 'edited_text') {
                        html += `<details><summary>View content changes</summary>
                                 <p><strong>Old:</strong><br>${log.oldValue}</p>
                                 <p><strong>New:</strong><br>${log.newValue}</p>
                                 </details>`;
                    }

                    html += `</div><hr>`;
                    logContainer.append(html);
                });
            } else {
                $('#logContainer').html('<p>Error fetching logs.</p>');
            }
        }
    });
}

function loadMessages() {
    const documentID = new URLSearchParams(window.location.search).get("documentID");

    $.ajax({
        type: 'GET',
        url: 'core/controller.php',
        data: { getMessages: 1, documentID },
        dataType: 'json',
        success: function (response) {
            if (response.status === "success") {
                const chatBox = $('#chatBox');
                chatBox.empty();
                response.messages.forEach(msg => {
                    chatBox.append(`<p><strong>${msg.username}</strong> [${msg.sentAt}]: ${msg.messageText}</p>`);
                });
                chatBox.scrollTop(chatBox[0].scrollHeight);
            }
        }
    });
}

$('#chatForm').on('submit', function (e) {
    e.preventDefault();

    const messageText = $('#chatInput').val().trim();
    const documentID = new URLSearchParams(window.location.search).get("documentID");

    if (!messageText) return;

    $.ajax({
        type: 'POST',
        url: 'core/controller.php',
        data: {
            sendMessage: 1,
            documentID,
            messageText
        },
        dataType: 'json',
        success: function (response) {
            if (response.status === "success") {
                $('#chatInput').val('');
                loadMessages();
            }
        }
    });
});
