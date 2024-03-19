<?php
/* Note: No credit is provided for submitting design and/or code that is     */
/*       taken from course-provided examples.                                */
/*                                                                           */
/* Do not copy this code into your project submission and then change it.    */
/*                                                                           */
/* Write your own code from scratch. Use this example as a REFERENCE only.   */
/*                                                                           */
/* You may not copy this code, change a few names/variables, and then claim  */
/* it as your own.                                                           */
/*                                                                           */
/* Examples are provided to help you learn. Copying the example and then     */
/* changing it a bit, does not help you learn the learning objectives of     */
/* this assignment. You need to write your own code from scratch to help you */
/* learn.                                                                    */

$page_title = "Transcript";

$nav_transcript_class = "active_page";

// key/value coding for academic terms
const TERM_CODINGS = array(
  101 => "2020FA",
  102 => "2021SP",
  103 => "2021FA",
  104 => "2022SP",
  105 => "2022FA",
  106 => "2023SP",
  107 => "2023FA",
  108 => "2024SP"
);

// key/value coding for academic year
const ACADEMIC_YEAR_CODINGS = array(
  1 => "First-Year",
  2 => "Sophomore",
  3 => "Junior",
  4 => "Senior"
);

// valid values for courses
const COURSES = array(
  "CS 1110",
  "INFO 1200",
  "INFO 1300",
  "INFO 2040",
  "INFO 2300",
  "INFO 2450",
  "INFO 2950",
  "INFO 3300",
  "MATH 1110",
  "MATH 1710"
);

// valid values for grades
const GRADES = array(
  "A+",
  "A",
  "A-",
  "B+",
  "B",
  "B-",
  "C+",
  "C",
  "C-",
  "D+",
  "D",
  "D-",
  "F"
);

// Did the user submit the form?
if (isset($_POST["request-insert"])) {

  $form_values["class_num"] = ($_POST["course"] == "" ? NULL : $_POST["course"]); // untrusted
  $form_values["term"]      = ($_POST["term"]   == "" ? NULL : (int)$_POST["term"]); // untrusted
  $form_values["year"]      = ($_POST["year"]   == "" ? NULL : (int)$_POST["year"]); // untrusted
  $form_values["grade"]     = ($_POST["grade"]  == "" ? NULL : $_POST["grade"]); // untrusted

  $result = exec_sql_query(
    $db,
    "INSERT INTO grades (class_num, term, acad_year, grade) VALUES (:course, :term, :acad_year, :grade);",
    array(
      ":course"    => $form_values["class_num"], // tainted/untrusted
      ":term"      => $form_values["term"], // tainted/untrusted
      ":acad_year" => $form_values["year"], // tainted/untrusted
      ":grade"     => $form_values["grade"] // tainted/untrusted
    )
  );
}

// query grades table
$result = exec_sql_query($db, "SELECT * FROM grades ORDER BY term ASC;");
$records = $result->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">

<?php include "includes/meta.php" ?>

<body>
  <?php include "includes/header.php" ?>

  <main class="transcript">
    <h2><?php echo $page_title; ?></h2>

    <table>
      <tr>
        <th>Class</th>
        <th>Term</th>
        <th>Year</th>
        <th class="min">Grade</th>
      </tr>

      <?php
      // write a table row for each record
      foreach ($records as $record) {
        $course = $record["class_num"];
        $term = TERM_CODINGS[$record["term"]];
        $year = ACADEMIC_YEAR_CODINGS[$record["acad_year"]];
        $grade = $record["grade"] ?? "";

        // row partial
        include "includes/transcript-record.php";
      } ?>

    </table>

    <section>
      <h2>Add Student Course Record</h2>

      <!-- Note: This form uses client-side validation. Client-side validation is prohibited in Project 2.
                 Your forms should always include the novalidate attribute to disable client-side validation. -->
      <form class="insert" action="/transcript" method="post">

        <div class="label-input">
          <label for="insert-course">Course:</label>
          <select id="insert-course" name="course" required>
            <option value="" disabled selected>Select Course</option>

            <?php foreach (COURSES as $course) { ?>
              <option value="<?php echo htmlspecialchars($course); ?>">
                <?php echo htmlspecialchars($course); ?>
              </option>
            <?php } ?>
          </select>
        </div>

        <div class="label-input">
          <label for="insert-term">Term:</label>
          <select id="insert-term" name="term" required>
            <option value="" disabled selected>Select Term</option>

            <?php foreach (TERM_CODINGS as $code => $term) { ?>
              <option value="<?php echo htmlspecialchars($code); ?>">
                <?php echo htmlspecialchars($term); ?>
              </option>
            <?php } ?>
          </select>
        </div>

        <div class="label-input">
          <label for="insert-year">Academic Year:</label>
          <select id="insert-year" name="year" required>
            <option value="" disabled selected>Select Year</option>

            <?php foreach (ACADEMIC_YEAR_CODINGS as $code => $year) { ?>
              <option value="<?php echo htmlspecialchars($code); ?>">
                <?php echo htmlspecialchars($year); ?>
              </option>
            <?php } ?>
          </select>
        </div>

        <div class="label-input">
          <label for="insert-grade">Grade:</label>
          <select id="insert-grade" name="grade">
            <option value="">No Grade</option>

            <?php foreach (GRADES as $grade) { ?>
              <option value="<?php echo htmlspecialchars($grade); ?>">
                <?php echo htmlspecialchars($grade); ?>
              </option>
            <?php } ?>
          </select>
        </div>

        <div class="align-right">
          <button type="submit" name="request-insert">
            Add Course Record
          </button>
        </div>
      </form>
    </section>

  </main>

  <?php include "includes/footer.php" ?>
</body>

</html>
