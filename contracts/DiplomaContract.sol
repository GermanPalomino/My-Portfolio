// SPDX-License-Identifier: MIT
pragma solidity ^0.8.0;

contract DiplomaContract {
    struct Diploma {
        string studentName;
        string course;
        string grade;
    }

    Diploma[] public diplomas;

    function createDiploma(string memory studentName, string memory course, string memory grade) public {
        diplomas.push(Diploma(studentName, course, grade));
    }
}