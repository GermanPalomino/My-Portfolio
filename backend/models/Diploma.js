const mongoose = require('mongoose');

const DiplomaSchema = new mongoose.Schema({
    studentName: String,
    course: String,
    grade: String,
    issueDate: { type: Date, default: Date.now }
});

module.exports = mongoose.model('Diploma', DiplomaSchema);