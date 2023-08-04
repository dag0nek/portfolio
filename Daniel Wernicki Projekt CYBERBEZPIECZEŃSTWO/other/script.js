const quizData = [
    {
        question: "Polegają na zalaniu serwerów bardzo dużą ilością informacji lub zadań?",
        a: "DDoS",
        b: "PHISING ",
        c: "Malware",
        correct: "a",
    },
    {
        question: "Kiedy cyberbezpieczeństwo zaczęło odgrywać ważną rolę?",
        a: "Przed XX wiekie",
        b: "Nigdy",
        c: "W XXI wieku",
        correct: "c",
    },
    {
        question: "PHISING to?",
        a: "Metoda oszustwa polegjąca na podszywaniu się",
        b: "Zwalanianie serwera bardzo dużą ilością informacji lub zadań",
        c: "Żadna odpowiedź nie jest prawidłowa",
        correct: "a",
    },
    {
        question: "Co to jest Cyberbezpieczeństwo?",
        a: "Ochrona sieci informatycznych, urządzeń, programów i danych przed atakami",
        b: "Ochrona urządzeń podłączonych do sieci",
        c: "Obie odpowiedzi są prawidłowe",
        correct: "c",
    },
];
const quiz= document.getElementById('quiz')
const answerEls = document.querySelectorAll('.answer')
const questionEl = document.getElementById('question')
const a_text = document.getElementById('a_text')
const b_text = document.getElementById('b_text')
const c_text = document.getElementById('c_text')
const d_text = document.getElementById('d_text')
const submitBtn = document.getElementById('submit')
let currentQuiz = 0
let score = 0
loadQuiz()
function loadQuiz() {
    deselectAnswers()
    const currentQuizData = quizData[currentQuiz]
    questionEl.innerText = currentQuizData.question
    a_text.innerText = currentQuizData.a
    b_text.innerText = currentQuizData.b
    c_text.innerText = currentQuizData.c
}
function deselectAnswers() {
    answerEls.forEach(answerEl => answerEl.checked = false)
}
function getSelected() {
    let answer
    answerEls.forEach(answerEl => {
        if(answerEl.checked) {
            answer = answerEl.id
        }
    })
    return answer
}
submitBtn.addEventListener('click', () => {
    const answer = getSelected()
    if(answer) {
       if(answer === quizData[currentQuiz].correct) {
           score++
       }
       currentQuiz++
       if(currentQuiz < quizData.length) {
           loadQuiz()
       } else {
           quiz.innerHTML = `
           <h2>twój wynik ${score}/${quizData.length} poprawnych odpowiedzi</h2>
           <button onclick="location.reload()">Jeszcze raz</button>
           `
       }
    }
})