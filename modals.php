<!-- Log Progress Modal -->
<div id="logProgressModal" class="modal" style="display:none;">
  <div class="modal-content">
    <span class="close" onclick="closeModal('logProgressModal')">&times;</span>
    <h2>Log Progress</h2>
    <form action="log_progress.php" method="POST">
      <label>Workout Type:</label>
      <input type="text" name="workout_type" required>
      <label>Duration (minutes):</label>
      <input type="number" name="duration" required>
      <button type="submit">Save</button>
    </form>
  </div>
</div>

<!-- Book Session Modal -->
<div id="bookSessionModal" class="modal" style="display:none;">
  <div class="modal-content">
    <span class="close" onclick="closeModal('bookSessionModal')">&times;</span>
    <h2>Book Session</h2>
    <form action="book_session.php" method="POST">
      <label>Date:</label>
      <input type="date" name="session_date" required>
      <label>Time:</label>
      <input type="time" name="session_time" required>
      <button type="submit">Book</button>
    </form>
  </div>
</div>
