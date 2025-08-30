const express = require('express');
const router = express.Router();
const Diploma = require('../models/Diploma');

// Crear diploma
router.post('/', async (req, res) => {
    try {
        const diploma = new Diploma(req.body);
        await diploma.save();
        res.status(201).json(diploma);
    } catch (err) {
        res.status(500).json({ error: 'Error al crear el diploma' });
    }
});

// Listar diplomas
router.get('/', async (req, res) => {
    try {
        const diplomas = await Diploma.find();
        res.status(200).json(diplomas);
    } catch (err) {
        res.status(500).json({ error: 'Error al listar diplomas' });
    }
});

module.exports = router;