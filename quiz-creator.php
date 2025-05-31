<?php
// index.php
// Questo file PHP serve solo a consegnare l'HTML; tutta la logica è implementata in JavaScript lato client.
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <!-- Assicura la corretta scala della viewport -->
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Universal Quiz Creator</title>
  
  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" />
  
  <style>
    /* Base styling */
    body {
      background-color: #f8f9fa;
      padding-top: 60px;
      padding-bottom: 60px;
    }
    .container {
      max-width: 960px;
    }
    h1 {
      text-align: center;
      margin-bottom: 30px;
    }
    /* Navigation Tabs */
    .nav-tabs .nav-link {
      cursor: pointer;
    }
    /* Sezioni generiche */
    .section {
      display: none;
      margin-top: 20px;
    }
    .section.active {
      display: block;
    }
    /* Create Quiz Form styling */
    .quiz-form .form-group {
      margin-bottom: 15px;
    }
    /* Question Block styling */
    .question-block {
      border: 1px solid #ddd;
      padding: 15px;
      margin-bottom: 15px;
      border-radius: 4px;
      background: #fff;
      position: relative;
    }
    .question-block .remove-question {
      position: absolute;
      top: 10px;
      right: 10px;
      font-size: 0.8rem;
    }
    /* Quiz Play styling */
    .quiz-play .question {
      border: 1px solid #ddd;
      padding: 15px;
      margin-bottom: 15px;
      border-radius: 4px;
      background: #fff;
    }
    /* Table styling in Manage Quizzes */
    table {
      width: 100%;
    }
    /* Set Management styling */
    .set-management .card-body {
      background-color: #fff;
    }
    .export-import-btns button {
      margin-right: 10px;
      margin-top: 10px;
    }
  </style>
</head>
<body>
  <div class="container">
    <!-- Application Title -->
    <h1>Universal Quiz Creator</h1>
    
    <!-- Navigation Tabs -->
    <ul class="nav nav-tabs" id="mainTabs">
      <li class="nav-item">
        <a class="nav-link active" data-target="createQuizSection">Create Quiz</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" data-target="playQuizSection">Play Quiz</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" data-target="manageQuizSection">Manage Quizzes</a>
      </li>
    </ul>
    
    <!-- Create Quiz Section -->
    <div id="createQuizSection" class="section active">
      <div class="card mb-4">
        <div class="card-header">Create New Quiz</div>
        <div class="card-body">
          <div class="mb-3">
            <label for="quizTitle" class="form-label">Quiz Title</label>
            <input type="text" id="quizTitle" class="form-control" placeholder="Enter quiz title" required>
          </div>
          <div class="mb-3">
            <button id="addQuestionBtn" class="btn btn-secondary">Add Question</button>
          </div>
          <!-- Container for question blocks -->
          <div id="questionsContainer"></div>
          <button id="saveQuizBtn" class="btn btn-primary">Save Quiz</button>
        </div>
      </div>
    </div>
    
    <!-- Play Quiz Section -->
    <div id="playQuizSection" class="section">
      <div class="card mb-4">
        <div class="card-header">Select a Quiz to Play</div>
        <div class="card-body">
          <select id="quizSelect" class="form-select mb-3">
            <!-- Populated dynamically -->
          </select>
          <button id="startQuizBtn" class="btn btn-primary">Start Quiz</button>
        </div>
      </div>
      <div id="quizPlayContainer" class="card quiz-play" style="display: none;">
        <div class="card-header">Quiz</div>
        <div class="card-body" id="quizFormContainer">
          <!-- Quiz form rendered here (all questions) -->
        </div>
        <div class="card-footer">
          <button id="exportQuizResultsJSONBtn" class="btn btn-success">Export Quiz Results (JSON)</button>
          <button id="exportQuizResultsCSVBtn" class="btn btn-info">Export Quiz Results (CSV)</button>
        </div>
      </div>
    </div>
    
    <!-- Manage Quizzes Section -->
    <div id="manageQuizSection" class="section">
      <div class="card mb-4">
        <div class="card-header">Manage Saved Quizzes</div>
        <div class="card-body">
          <table class="table" id="quizTable">
            <thead>
              <tr>
                <th>Quiz Title</th>
                <th># Questions</th>
                <th>Last Result</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <!-- Rows loaded dynamically -->
            </tbody>
          </table>
          <div class="export-import-btns">
            <button id="exportQuizJSONBtn" class="btn btn-success">Export Quizzes (JSON)</button>
            <button id="exportQuizCSVBtn" class="btn btn-info">Export Quizzes (CSV)</button>
            <input type="file" id="importQuizInput" style="display:none;" accept="application/json">
            <button id="importQuizBtn" class="btn btn-outline-success">Import Quizzes (JSON)</button>
            <button id="printQuizBtn" class="btn btn-secondary">Print Quizzes</button>
          </div>
        </div>
      </div>
    </div>
    
    <!-- EDIT QUIZ MODAL -->
    <div class="modal fade" id="editQuizModal" tabindex="-1" aria-labelledby="editQuizModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="editQuizModalLabel">Edit Quiz</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form id="editQuizForm">
              <div class="mb-3">
                <label for="editQuizTitle" class="form-label">Quiz Title</label>
                <input type="text" id="editQuizTitle" class="form-control" required>
              </div>
              <div class="mb-3">
                <button type="button" id="editAddQuestionBtn" class="btn btn-secondary">Add Question</button>
              </div>
              <div id="editQuestionsContainer"></div>
              <button type="submit" class="btn btn-primary">Save Changes</button>
            </form>
          </div>
        </div>
      </div>
    </div>
    
  </div> <!-- End Container -->
  
  <!-- Bootstrap JS Bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  
  <script>
    /**************************************************************************************************
     * UNIVERSAL QUIZ CREATOR WEBAPP
     *
     * This app allows teachers to:
     * - Create a quiz: enter a title and add as many questions as desired.
     *   Each question includes: question text, 4 options, and a dropdown to select the correct answer.
     *   Each question block is removable.
     * - Save the quiz to localStorage (key "quizzes"), export/import it in JSON and CSV,
     *   and save the quiz structure for office use.
     * - Play a quiz: select a saved quiz, answer all questions (rendered as one form), and on submit, the score is computed
     *   and saved in the quiz (property "lastResult"). The quiz played can be exported in JSON/CSV.
     * - Manage quizzes: view a table listing all quizzes (title, # questions, last result), with options to delete 
     *   or edit a quiz. Editing opens a modal that allows modifying quiz title, questions and answers.
     ****************************************************************************************************/
    
    // GLOBAL STORAGE
    let quizzes = JSON.parse(localStorage.getItem("quizzes")) || [];
    // Each quiz: { id, title, questions: [ { question, options: [..., ..., ..., ...], correctIndex } ], lastResult: { score, total, timestamp } (optional) }
    function saveQuizzes() {
      localStorage.setItem("quizzes", JSON.stringify(quizzes));
    }
    
    // ---------------------------
    // CREATE QUIZ SECTION
    // ---------------------------
    function addQuestionBlock() {
      // Create new question block element
      const container = document.getElementById("questionsContainer");
      const block = document.createElement("div");
      block.className = "question-block";
      block.innerHTML = `
        <div class="card">
          <div class="card-header">
            Question
            <button type="button" class="btn btn-sm btn-danger remove-question float-end">Remove</button>
          </div>
          <div class="card-body">
            <div class="form-group mb-2">
              <label class="form-label">Question Text</label>
              <input type="text" class="form-control question-text" placeholder="Enter question text" required>
            </div>
            <div class="form-group mb-2">
              <label class="form-label">Options</label>
              <div class="d-grid gap-2">
                <input type="text" class="form-control option-input" placeholder="Option 1" required>
                <input type="text" class="form-control option-input" placeholder="Option 2" required>
                <input type="text" class="form-control option-input" placeholder="Option 3" required>
                <input type="text" class="form-control option-input" placeholder="Option 4" required>
              </div>
            </div>
            <div class="form-group">
              <label class="form-label">Correct Answer</label>
              <select class="form-select correct-option" required>
                <option value="0">Option 1</option>
                <option value="1">Option 2</option>
                <option value="2">Option 3</option>
                <option value="3">Option 4</option>
              </select>
            </div>
          </div>
        </div>
      `;
      block.querySelector(".remove-question").addEventListener("click", () => {
        block.remove();
      });
      container.appendChild(block);
    }
    document.getElementById("addQuestionBtn").addEventListener("click", addQuestionBlock);
    
    document.getElementById("saveQuizBtn").addEventListener("click", () => {
      const title = document.getElementById("quizTitle").value.trim();
      if (!title) {
        alert("Please enter a quiz title.");
        return;
      }
      const blocks = document.querySelectorAll("#questionsContainer .question-block");
      if (blocks.length === 0) {
        alert("Please add at least one question.");
        return;
      }
      let questions = [];
      let valid = true;
      blocks.forEach(block => {
        const text = block.querySelector(".question-text").value.trim();
        let opts = [];
        block.querySelectorAll(".option-input").forEach(input => {
          opts.push(input.value.trim());
        });
        const correct = parseInt(block.querySelector(".correct-option").value);
        if (!text || opts.some(o => o === "")) {
          valid = false;
        }
        questions.push({
          question: text,
          options: opts,
          correctIndex: correct
        });
      });
      if (!valid) {
        alert("Please complete all fields for each question.");
        return;
      }
      const newQuiz = {
        id: Date.now().toString(),
        title: title,
        questions: questions,
        lastResult: null
      };
      quizzes.push(newQuiz);
      saveQuizzes();
      alert("Quiz saved successfully!");
      document.getElementById("quizTitle").value = "";
      document.getElementById("questionsContainer").innerHTML = "";
      loadQuizSelectOptions();
      loadQuizTable();
    });
    
    // ---------------------------
    // PLAY QUIZ SECTION
    // ---------------------------
    function loadQuizSelectOptions() {
      const select = document.getElementById("quizSelect");
      select.innerHTML = "";
      quizzes.forEach(quiz => {
        const opt = document.createElement("option");
        opt.value = quiz.id;
        opt.textContent = quiz.title;
        select.appendChild(opt);
      });
    }
    loadQuizSelectOptions();
    
    document.getElementById("startQuizBtn").addEventListener("click", () => {
      const select = document.getElementById("quizSelect");
      const quizId = select.value;
      const quiz = quizzes.find(q => q.id === quizId);
      if (!quiz) {
        alert("Please select a valid quiz.");
        return;
      }
      renderQuizForm(quiz);
      document.getElementById("quizPlayContainer").style.display = "block";
    });
    
    function renderQuizForm(quiz) {
      const container = document.getElementById("quizFormContainer");
      container.innerHTML = "";
      const form = document.createElement("form");
      quiz.questions.forEach((q, idx) => {
        const qDiv = document.createElement("div");
        qDiv.className = "question mb-3";
        const qTitle = document.createElement("h5");
        qTitle.textContent = `Q${idx + 1}: ${q.question}`;
        qDiv.appendChild(qTitle);
        q.options.forEach((option, optIdx) => {
          const optionDiv = document.createElement("div");
          optionDiv.className = "form-check";
          const radio = document.createElement("input");
          radio.type = "radio";
          radio.className = "form-check-input";
          radio.name = "q" + idx;
          radio.value = optIdx;
          radio.id = `q${idx}_opt${optIdx}`;
          optionDiv.appendChild(radio);
          const label = document.createElement("label");
          label.className = "form-check-label";
          label.htmlFor = radio.id;
          label.textContent = option;
          optionDiv.appendChild(label);
          qDiv.appendChild(optionDiv);
        });
        form.appendChild(qDiv);
      });
      const submitBtn = document.createElement("button");
      submitBtn.type = "submit";
      submitBtn.className = "btn btn-primary";
      submitBtn.textContent = "Submit Quiz";
      form.appendChild(submitBtn);
      
      form.addEventListener("submit", (e) => {
        e.preventDefault();
        let score = 0;
        quiz.questions.forEach((q, idx) => {
          const radios = form.elements["q" + idx];
          let selected;
          if (radios) {
            if (radios.length === undefined) {
              selected = radios.value;
            } else {
              for (let radio of radios) {
                if (radio.checked) {
                  selected = radio.value;
                  break;
                }
              }
            }
          }
          if (parseInt(selected) === q.correctIndex) {
            score++;
          }
        });
        const result = {
          score: score,
          total: quiz.questions.length,
          timestamp: new Date().toISOString()
        };
        quiz.lastResult = result;
        saveQuizzes();
        alert(`You scored ${score} out of ${quiz.questions.length}`);
      });
      
      container.appendChild(form);
    }
    
    // ---------------------------
    // MANAGE QUIZZES SECTION
    // ---------------------------
    // Load quizzes into a table with options to delete and edit
    function loadQuizTable() {
      const tbody = document.querySelector("#quizTable tbody");
      tbody.innerHTML = "";
      quizzes.forEach(quiz => {
        const tr = document.createElement("tr");
        const tdTitle = document.createElement("td");
        tdTitle.textContent = quiz.title;
        const tdNum = document.createElement("td");
        tdNum.textContent = quiz.questions.length;
        const tdResult = document.createElement("td");
        if (quiz.lastResult) {
          tdResult.innerHTML = `${quiz.lastResult.score}/${quiz.lastResult.total}<br>${new Date(quiz.lastResult.timestamp).toLocaleString()}`;
        } else {
          tdResult.textContent = "N/A";
        }
        const tdActions = document.createElement("td");
        // Edit button
        const editBtn = document.createElement("button");
        editBtn.className = "btn btn-sm btn-warning";
        editBtn.style.marginRight = "5px";
        editBtn.textContent = "Edit";
        editBtn.addEventListener("click", () => {
          openEditQuizModal(quiz.id);
        });
        // Delete button
        const delBtn = document.createElement("button");
        delBtn.className = "btn btn-sm btn-danger";
        delBtn.textContent = "Delete";
        delBtn.addEventListener("click", () => {
          if (confirm(`Delete quiz "${quiz.title}"?`)) {
            quizzes = quizzes.filter(q => q.id !== quiz.id);
            saveQuizzes();
            loadQuizTable();
            loadQuizSelectOptions();
          }
        });
        tdActions.appendChild(editBtn);
        tdActions.appendChild(delBtn);
        tr.appendChild(tdTitle);
        tr.appendChild(tdNum);
        tr.appendChild(tdResult);
        tr.appendChild(tdActions);
        tbody.appendChild(tr);
      });
    }
    loadQuizTable();
    
    // ---------------------------
    // EDIT QUIZ MODAL (Editing an existing quiz)
    // ---------------------------
    let currentEditingQuizId = null;
    function openEditQuizModal(quizId) {
      const quiz = quizzes.find(q => q.id === quizId);
      if (!quiz) return;
      currentEditingQuizId = quizId;
      document.getElementById("editQuizTitle").value = quiz.title;
      const container = document.getElementById("editQuestionsContainer");
      container.innerHTML = "";
      quiz.questions.forEach(q => {
        container.appendChild(createEditQuestionBlock(q));
      });
      // Show modal
      const modalEl = document.getElementById("editQuizModal");
      const editModal = new bootstrap.Modal(modalEl);
      editModal.show();
    }
    
    // Create a question block for Edit Quiz modal from existing question data
    function createEditQuestionBlock(questionData) {
      const block = document.createElement("div");
      block.className = "question-block mb-3";
      block.innerHTML = `
        <div class="card">
          <div class="card-header">
            Question
            <button type="button" class="btn btn-sm btn-danger remove-question float-end">Remove</button>
          </div>
          <div class="card-body">
            <div class="form-group mb-2">
              <label class="form-label">Question Text</label>
              <input type="text" class="form-control question-text" required>
            </div>
            <div class="form-group mb-2">
              <label class="form-label">Options</label>
              <div class="d-grid gap-2">
                <input type="text" class="form-control option-input" placeholder="Option 1" required>
                <input type="text" class="form-control option-input" placeholder="Option 2" required>
                <input type="text" class="form-control option-input" placeholder="Option 3" required>
                <input type="text" class="form-control option-input" placeholder="Option 4" required>
              </div>
            </div>
            <div class="form-group">
              <label class="form-label">Correct Answer</label>
              <select class="form-select correct-option" required>
                <option value="0">Option 1</option>
                <option value="1">Option 2</option>
                <option value="2">Option 3</option>
                <option value="3">Option 4</option>
              </select>
            </div>
          </div>
        </div>
      `;
      // Prefill data
      block.querySelector(".question-text").value = questionData.question;
      const optionInputs = block.querySelectorAll(".option-input");
      questionData.options.forEach((opt, i) => {
        optionInputs[i].value = opt;
      });
      block.querySelector(".correct-option").value = questionData.correctIndex;
      block.querySelector(".remove-question").addEventListener("click", () => {
        block.remove();
      });
      return block;
    }
    
    // Listener for "Add Question" in Edit Quiz modal
    document.getElementById("editAddQuestionBtn").addEventListener("click", () => {
      const container = document.getElementById("editQuestionsContainer");
      // Append an empty question block
      container.appendChild(createEditQuestionBlock({ question: "", options: ["", "", "", ""], correctIndex: 0 }));
    });
    
    // Save Changes in Edit Quiz modal
    document.getElementById("editQuizForm").addEventListener("submit", (e) => {
      e.preventDefault();
      const newTitle = document.getElementById("editQuizTitle").value.trim();
      if (!newTitle) {
        alert("Please enter a quiz title.");
        return;
      }
      const blocks = document.querySelectorAll("#editQuestionsContainer .question-block");
      if (blocks.length === 0) {
        alert("Please add at least one question.");
        return;
      }
      let newQuestions = [];
      let valid = true;
      blocks.forEach(block => {
        const qt = block.querySelector(".question-text").value.trim();
        let opts = [];
        block.querySelectorAll(".option-input").forEach(input => {
          opts.push(input.value.trim());
        });
        let correct = parseInt(block.querySelector(".correct-option").value);
        if (!qt || opts.some(o => o === "")) {
          valid = false;
        }
        newQuestions.push({
          question: qt,
          options: opts,
          correctIndex: correct
        });
      });
      if (!valid) {
        alert("Please complete all fields for every question.");
        return;
      }
      // Update the quiz in the quizzes array
      const quiz = quizzes.find(q => q.id === currentEditingQuizId);
      if (!quiz) return;
      quiz.title = newTitle;
      quiz.questions = newQuestions;
      saveQuizzes();
      alert("Quiz updated successfully!");
      loadQuizSelectOptions();
      loadQuizTable();
      // Close modal
      const editModalEl = document.getElementById("editQuizModal");
      const editModal = bootstrap.Modal.getInstance(editModalEl);
      editModal.hide();
    });
    
    // ---------------------------
    // MANAGE QUIZZES SECTION
    // ---------------------------
    function loadQuizTable() {
      const tbody = document.querySelector("#quizTable tbody");
      tbody.innerHTML = "";
      quizzes.forEach(quiz => {
        const tr = document.createElement("tr");
        // Quiz title
        const tdTitle = document.createElement("td");
        tdTitle.textContent = quiz.title;
        // Number of questions
        const tdNum = document.createElement("td");
        tdNum.textContent = quiz.questions.length;
        // Last result
        const tdResult = document.createElement("td");
        if (quiz.lastResult) {
          tdResult.innerHTML = `${quiz.lastResult.score}/${quiz.lastResult.total}<br>${new Date(quiz.lastResult.timestamp).toLocaleString()}`;
        } else {
          tdResult.textContent = "N/A";
        }
        // Actions: Edit and Delete
        const tdActions = document.createElement("td");
        const editBtn = document.createElement("button");
        editBtn.className = "btn btn-sm btn-warning";
        editBtn.style.marginRight = "5px";
        editBtn.textContent = "Edit";
        editBtn.addEventListener("click", () => {
          openEditQuizModal(quiz.id);
        });
        const deleteBtn = document.createElement("button");
        deleteBtn.className = "btn btn-sm btn-danger";
        deleteBtn.textContent = "Delete";
        deleteBtn.addEventListener("click", () => {
          if (confirm(`Delete quiz "${quiz.title}"?`)) {
            quizzes = quizzes.filter(q => q.id !== quiz.id);
            saveQuizzes();
            loadQuizTable();
            loadQuizSelectOptions();
          }
        });
        tdActions.appendChild(editBtn);
        tdActions.appendChild(deleteBtn);
        tr.appendChild(tdTitle);
        tr.appendChild(tdNum);
        tr.appendChild(tdResult);
        tr.appendChild(tdActions);
        tbody.appendChild(tr);
      });
    }
    loadQuizTable();
    
    // ---------------------------
    // EXPORT & IMPORT QUIZZES
    // ---------------------------
    document.getElementById("exportQuizJSONBtn").addEventListener("click", () => {
      if (quizzes.length === 0) {
        alert("No quizzes to export.");
        return;
      }
      const jsonContent = JSON.stringify(quizzes, null, 2);
      const blob = new Blob([jsonContent], { type: "application/json;charset=utf-8" });
      const url = URL.createObjectURL(blob);
      const a = document.createElement("a");
      a.href = url;
      a.download = "quizzes.json";
      document.body.appendChild(a);
      a.click();
      document.body.removeChild(a);
      URL.revokeObjectURL(url);
    });
    
    document.getElementById("exportQuizCSVBtn").addEventListener("click", () => {
      if (quizzes.length === 0) {
        alert("No quizzes to export.");
        return;
      }
      let rows = [];
      quizzes.forEach(quiz => {
        quiz.questions.forEach((q, idx) => {
          rows.push({
            quiz_id: quiz.id,
            quiz_title: quiz.title,
            question: q.question,
            option1: q.options[0],
            option2: q.options[1],
            option3: q.options[2],
            option4: q.options[3],
            correct_index: q.correctIndex
          });
        });
      });
      const headers = ["quiz_id", "quiz_title", "question", "option1", "option2", "option3", "option4", "correct_index"];
      let csv = headers.join(",") + "\n";
      rows.forEach(row => {
        const rowData = headers.map(header => `"${String(row[header]).replace(/"/g, '""')}"`).join(",");
        csv += rowData + "\n";
      });
      const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
      const url = URL.createObjectURL(blob);
      const link = document.createElement("a");
      link.href = url;
      link.download = "quizzes.csv";
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
    });
    
    document.getElementById("importQuizBtn").addEventListener("click", () => {
      document.getElementById("importQuizInput").click();
    });
    document.getElementById("importQuizInput").addEventListener("change", (e) => {
      const file = e.target.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = (e) => {
          try {
            const imported = JSON.parse(e.target.result);
            quizzes = imported;
            saveQuizzes();
            alert("Quizzes imported successfully.");
            loadQuizSelectOptions();
            loadQuizTable();
          } catch (err) {
            alert("Error reading JSON file.");
          }
        };
        reader.readAsText(file);
      }
    });
    
    document.getElementById("printQuizBtn").addEventListener("click", () => {
      window.print();
    });
    
    // ---------------------------
    // EXPORT QUIZ RESULTS
    // ---------------------------
    document.getElementById("exportQuizResultsJSONBtn").addEventListener("click", () => {
      const select = document.getElementById("quizSelect");
      const quiz = quizzes.find(q => q.id === select.value);
      if (!quiz || !quiz.lastResult) {
        alert("No quiz result available to export.");
        return;
      }
      const jsonContent = JSON.stringify(quiz.lastResult, null, 2);
      const blob = new Blob([jsonContent], { type: "application/json;charset=utf-8" });
      const url = URL.createObjectURL(blob);
      const a = document.createElement("a");
      a.href = url;
      a.download = "quiz_result.json";
      document.body.appendChild(a);
      a.click();
      document.body.removeChild(a);
      URL.revokeObjectURL(url);
    });
    
    document.getElementById("exportQuizResultsCSVBtn").addEventListener("click", () => {
      const select = document.getElementById("quizSelect");
      const quiz = quizzes.find(q => q.id === select.value);
      if (!quiz || !quiz.lastResult) {
        alert("No quiz result available to export.");
        return;
      }
      const headers = ["score", "total", "timestamp"];
      let csv = headers.join(",") + "\n";
      const row = headers.map(h => `"${String(quiz.lastResult[h]).replace(/"/g, '""')}"`).join(",");
      csv += row + "\n";
      const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
      const url = URL.createObjectURL(blob);
      const link = document.createElement("a");
      link.href = url;
      link.download = "quiz_result.csv";
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
    });
    
    // ---------------------------
    // TAB NAVIGATION
    // ---------------------------
    const mainTabs = document.getElementById("mainTabs");
    mainTabs.addEventListener("click", (e) => {
      const targetTab = e.target.closest(".nav-link");
      if (!targetTab) return;
      Array.from(mainTabs.children).forEach(li => {
        li.querySelector(".nav-link").classList.remove("active");
      });
      targetTab.classList.add("active");
      const sections = document.querySelectorAll(".section");
      sections.forEach(sec => sec.classList.remove("active"));
      const targetId = targetTab.getAttribute("data-target");
      document.getElementById(targetId).classList.add("active");
    });
    
    // ---------------------------
    // INITIALIZATION
    // ---------------------------
    loadQuizSelectOptions();
    loadQuizTable();
    
  </script>
  
  <!-- EDIT QUIZ MODAL -->
  <div class="modal fade" id="editQuizModal" tabindex="-1" aria-labelledby="editQuizModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editQuizModalLabel">Edit Quiz</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="editQuizForm">
            <div class="mb-3">
              <label for="editQuizTitle" class="form-label">Quiz Title</label>
              <input type="text" id="editQuizTitle" class="form-control" required>
            </div>
            <div class="mb-3">
              <button type="button" id="editAddQuestionBtn" class="btn btn-secondary">Add Question</button>
            </div>
            <div id="editQuestionsContainer"></div>
            <button type="submit" class="btn btn-primary">Save Changes</button>
          </form>
        </div>
      </div>
    </div>
  </div>
  
  <script>
    // ---------------------------
    // EDIT QUIZ FUNCTIONALITY
    // ---------------------------
    let currentEditingQuizId = null;
    // Open Edit Quiz Modal and prefill with quiz data
    function openEditQuizModal(quizId) {
      const quiz = quizzes.find(q => q.id === quizId);
      if (!quiz) return;
      currentEditingQuizId = quizId;
      document.getElementById("editQuizTitle").value = quiz.title;
      const container = document.getElementById("editQuestionsContainer");
      container.innerHTML = "";
      quiz.questions.forEach(q => {
        container.appendChild(createEditQuestionBlock(q));
      });
      const modalEl = document.getElementById("editQuizModal");
      const editModal = new bootstrap.Modal(modalEl);
      editModal.show();
    }
    
    // Create a question block for editing quiz questions with prefilled values
    function createEditQuestionBlock(questionData) {
      const block = document.createElement("div");
      block.className = "question-block mb-3";
      block.innerHTML = `
        <div class="card">
          <div class="card-header">
            Question
            <button type="button" class="btn btn-sm btn-danger remove-question float-end">Remove</button>
          </div>
          <div class="card-body">
            <div class="form-group mb-2">
              <label class="form-label">Question Text</label>
              <input type="text" class="form-control question-text" required>
            </div>
            <div class="form-group mb-2">
              <label class="form-label">Options</label>
              <div class="d-grid gap-2">
                <input type="text" class="form-control option-input" placeholder="Option 1" required>
                <input type="text" class="form-control option-input" placeholder="Option 2" required>
                <input type="text" class="form-control option-input" placeholder="Option 3" required>
                <input type="text" class="form-control option-input" placeholder="Option 4" required>
              </div>
            </div>
            <div class="form-group">
              <label class="form-label">Correct Answer</label>
              <select class="form-select correct-option" required>
                <option value="0">Option 1</option>
                <option value="1">Option 2</option>
                <option value="2">Option 3</option>
                <option value="3">Option 4</option>
              </select>
            </div>
          </div>
        </div>
      `;
      // Prefill fields
      block.querySelector(".question-text").value = questionData.question;
      const optionInputs = block.querySelectorAll(".option-input");
      questionData.options.forEach((opt, i) => {
        optionInputs[i].value = opt;
      });
      block.querySelector(".correct-option").value = questionData.correctIndex;
      // Remove event for block removal
      block.querySelector(".remove-question").addEventListener("click", () => {
        block.remove();
      });
      return block;
    }
    
    // Listener for adding a new question in Edit Quiz Modal
    document.getElementById("editAddQuestionBtn").addEventListener("click", () => {
      const container = document.getElementById("editQuestionsContainer");
      container.appendChild(createEditQuestionBlock({question:"", options:["","","",""], correctIndex:0}));
    });
    
    // Save changes from Edit Quiz Modal
    document.getElementById("editQuizForm").addEventListener("submit", (e) => {
      e.preventDefault();
      const newTitle = document.getElementById("editQuizTitle").value.trim();
      if (!newTitle) {
        alert("Please enter a quiz title.");
        return;
      }
      const blocks = document.querySelectorAll("#editQuestionsContainer .question-block");
      if (blocks.length === 0) {
        alert("Please add at least one question.");
        return;
      }
      let newQuestions = [];
      let valid = true;
      blocks.forEach(block => {
        const qText = block.querySelector(".question-text").value.trim();
        let opts = [];
        block.querySelectorAll(".option-input").forEach(input => {
          opts.push(input.value.trim());
        });
        const correct = parseInt(block.querySelector(".correct-option").value);
        if (!qText || opts.some(o => o === "")) {
          valid = false;
        }
        newQuestions.push({
          question: qText,
          options: opts,
          correctIndex: correct
        });
      });
      if (!valid) {
        alert("Please complete all fields for every question.");
        return;
      }
      const quiz = quizzes.find(q => q.id === currentEditingQuizId);
      if (!quiz) return;
      quiz.title = newTitle;
      quiz.questions = newQuestions;
      saveQuizzes();
      alert("Quiz updated successfully!");
      loadQuizSelectOptions();
      loadQuizTable();
      // Close modal
      const modalEl = document.getElementById("editQuizModal");
      const modalInstance = bootstrap.Modal.getInstance(modalEl);
      modalInstance.hide();
    });
    
  </script>
</body>
</html>
