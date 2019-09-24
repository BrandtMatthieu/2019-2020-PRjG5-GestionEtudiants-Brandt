<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class GestionController extends Controller
{
    /**
     * Get a list of all students
     */
    public function allStudents()
    {
		return DB::select('
		select idStudent, firstName, lastName
		from students;');
    }

	/**
	 * Adds a new student into the database
	 */
	public function registerStudent($firstName, $lastName, $idStudent)
	{
		if (empty($idStudent) && !idStudentExists($idStudent) && is_numeric($idStudent) && $idStudent > 0) {
			DB::insert('
			insert into students (firstName, lastName)
			values (?, ?);', [$firstName, $lastName]);
		} else {
			$nextIdStudent = getNextIdStudent();
			DB::insert('
			insert into students (idStudent, firstName, lastName)
			values (?, ?);', [$nextIdStudent, $firstName, $lastName]);
		}
		return;
	}

	/**
	 * Checks if the id already exists
	 */
	private function idStudentExists($idStudent)
	{
		return sizeof(DB::select('
		select count(*) as c
		from students
		where idStudent = ?;', [$idStudent])) > 0;
	}

	/**
	 * Get a free student id
	 */
	private function getNextIdStudent()
	{
		return DB::select('
		select max(idStudent)
		from students;') + 1;
	}

	/**
	 * Get a list of all courses
	 */
	public function allCourses()
	{
		return DB::select('
		select idCourse, courseLabel, courseDescription
		from courses;');
	}

	/**
	 * Get all courses a student is signed up to
	 */
	public function getCoursesOfStudent($idStudent)
	{
		return DB::select('
		select courses.idCourse, courses.courseLabel, courses.courseDescription
		from courses
		join subscriptions on courses.idCourse = subscriptions.idCourse
		where idStudent = ?', [$idStudent]);
	}

	/**
	 * Get all courses a student is not signed up to
	 */
	public function getMissingCoursesOfStudents($idStudent)
	{
		return DB::select('
		select courses.idCourse, courses.courseLabel, courses.courseDescription 
		from courses 
		where idCourse not in (
			select idCourse from subscriptions where idStudent = ?
		);
		', [$idStudent]);
	}

	public function studentSubscription($idStudent,$idCourse)
	{
		DB::insert('
		insert into subscriptions (idStudent, idCourse)
		values (?, ?);', [$idStudent, $idCourse]);
		return;
	}

	public function unSubscribeStudent($idStudent,$idCourse)
	{
		DB::delete('
		delete from subscriptions
		where idStudent = ? and idcourse = ? ', [$idStudent, $idCourse]);
		return;
	}
}
