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
<div id="bookSessionModal" class="modal" style="display:none; position:fixed; top:0; position:center; width:100%; height:100%; background:rgba(0,0,0,0.6); justify-content:center; align-items:center; z-index:9999;">
  <div class="modal-content" style="
        background: var(--panel, #121722); 
        color: var(--text, #eaf2ff); 
        padding:24px; 
        border-radius:20px; 
        width:360px; 
        position:relative; 
        font-family:system-ui;
        box-shadow:0 10px 25px rgba(0,0,0,0.3);
        display:flex;
        flex-direction:column;
        gap:12px;
      ">
    <span class="close" onclick="closeModal('bookSessionModal')" style="position:absolute; top:12px; right:16px; cursor:pointer; font-weight:bold; font-size:18px;">&times;</span>
    <h2 style="margin-bottom:12px; font-weight:800; font-size:20px; text-align:center;">Book a Session</h2>

    <div id="bookingMsg" style="margin-bottom:10px; font-size:14px; text-align:center;"></div>

    <form id="bookingForm" style="display:flex; flex-direction:column; gap:10px;">
      <input type="text" name="user_name" placeholder="Name" required style="padding:10px; border-radius:12px; border:none; background:rgba(255,255,255,0.05); color:#eaf2ff; font-size:14px;">
      <input type="number" name="user_age" placeholder="Age" required min="10" max="100" style="padding:10px; border-radius:12px; border:none; background:rgba(255,255,255,0.05); color:#eaf2ff; font-size:14px;">
      <input type="date" name="session_date" required style="padding:10px; border-radius:12px; border:none; background:rgba(255,255,255,0.05); color:#eaf2ff; font-size:14px;">
      <input type="time" name="session_time" required style="padding:10px; border-radius:12px; border:none; background:rgba(255,255,255,0.05); color:#eaf2ff; font-size:14px;">
      <select name="gym_type" required style="padding:10px; border-radius:12px; border:none; background:rgba(255,255,255,0.05); color:#eaf2ff; font-size:14px;">
        <option value="">-- Select Gym Type --</option>
        <option value="Weight Gain">Weight Gain</option>
        <option value="Sauna">Sauna</option>
        <option value="Strength Training">Strength Training</option>
        <option value="Fat Loss">Fat Loss</option>
        <option value="Cardio">Cardio</option>
      </select>
      <label style="display:flex; align-items:center; gap:8px; font-size:13px; color:var(--muted,#8da3b8);">
        <input type="checkbox" name="agree_conditions" required style="width:16px; height:16px;">
        I agree with the <a href="#" style="color:#39e6dd; text-decoration:none;">conditions</a>
      </label>
      <button type="submit" style="
        padding:12px; 
        border-radius:14px; 
        border:none; 
        cursor:pointer; 
        font-weight:800; 
        font-size:14px; 
        background:linear-gradient(180deg,#39e6dd,#2ac3bd); 
        color:#02161a;
        transition:all 0.2s ease;
      " 
      onmouseover="this.style.filter='brightness(1.1)'" 
      onmouseout="this.style.filter='brightness(1)'">Book Session</button>
    </form>
  </div>
</div>
<script>
const bookingForm = document.getElementById('bookingForm');
const bookingMsg = document.getElementById('bookingMsg');

bookingForm.addEventListener('submit', function(e){
    e.preventDefault(); // prevent normal form submission

    const formData = new FormData(bookingForm);

    fetch('booking.php', { // make sure this points to your PHP script
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if(data.success){
            bookingMsg.style.color = 'lightgreen';
            bookingMsg.textContent = data.message;
            bookingForm.reset(); // clear form
        } else {
            bookingMsg.style.color = 'red';
            bookingMsg.textContent = data.message;
        }
    })
    .catch(err => {
        bookingMsg.style.color = 'red';
        bookingMsg.textContent = 'Error booking session. Try again.';
        console.error(err);
    });
});
</script>
