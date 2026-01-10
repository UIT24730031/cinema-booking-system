<?php 
include('header.php'); 

if(!isset($_SESSION['user'])) {
    echo "<script>alert('Vui lòng đăng nhập để đặt vé!'); window.location='login.php';</script>";
    exit;
}

if(!isset($_GET['screening_id'])) {
    header('location: index.php');
    exit;
}

$screening_id = mysqli_real_escape_string($con, $_GET['screening_id']);
$qry = mysqli_query($con, "
    SELECT s.*, m.movie_name, m.image, m.duration, m.genre, t.name as theatre_name, t.address
    FROM tbl_screenings s
    JOIN tbl_movie m ON s.movie_id = m.movie_id
    JOIN tbl_theatre t ON s.theatre_id = t.id
    WHERE s.screening_id = '$screening_id'
");

if(mysqli_num_rows($qry) == 0) {
    echo "<script>alert('Lịch chiếu không tồn tại!'); window.location='index.php';</script>";
    exit;
}

$screening = mysqli_fetch_array($qry);

// Lấy ghế đã đặt
$booked_seats_qry = mysqli_query($con, "SELECT seats FROM tbl_bookings WHERE screening_id='$screening_id' AND status!='cancelled'");
$booked_seats = [];
while($row = mysqli_fetch_array($booked_seats_qry)) {
    $seats = explode(',', $row['seats']);
    $booked_seats = array_merge($booked_seats, $seats);
}
?>

<div class="container" style="margin-top: 30px; margin-bottom: 50px;">
    <div class="row">
        <!-- Thông tin phim -->
        <div class="col-md-4">
            <div style="background: #1a1a1a; padding: 20px; border-radius: 10px; position: sticky; top: 80px;">
                <img src="<?php echo $screening['image'];?>" class="img-responsive" style="width: 100%; border-radius: 8px; margin-bottom: 15px;">
                <h4 style="color: #fff; margin-bottom: 15px;"><?php echo htmlspecialchars($screening['movie_name']);?></h4>
                <p style="color: #aaa; margin-bottom: 10px;">
                    <i class="fas fa-map-marker-alt" style="color: #e50914;"></i> 
                    <strong><?php echo htmlspecialchars($screening['theatre_name']);?></strong>
                </p>
                <p style="color: #999; font-size: 13px; margin-bottom: 15px;">
                    <?php echo htmlspecialchars($screening['address']);?>
                </p>
                <p style="color: #aaa; margin-bottom: 10px;">
                    <i class="fas fa-calendar" style="color: #e50914;"></i> 
                    <?php echo date('d/m/Y', strtotime($screening['show_date']));?>
                </p>
                <p style="color: #aaa; margin-bottom: 10px;">
                    <i class="fas fa-clock" style="color: #e50914;"></i> 
                    <?php echo date('H:i', strtotime($screening['show_time']));?>
                </p>
                <p style="color: #aaa; margin-bottom: 15px;">
                    <i class="fas fa-desktop" style="color: #e50914;"></i> 
                    <?php echo htmlspecialchars($screening['screen_name']);?>
                </p>
                
                <div style="border-top: 1px solid #333; padding-top: 15px; margin-top: 15px;">
                    <p style="color: #aaa; margin-bottom: 10px;">Giá vé: <span style="color: #fff; font-size: 18px; font-weight: bold;"><?php echo number_format($screening['price'], 0, ',', '.');?>đ</span></p>
                    <p style="color: #aaa; margin-bottom: 10px;">Số ghế: <span id="seat-count" style="color: #fff; font-weight: bold;">0</span></p>
                    <p style="color: #aaa; margin-bottom: 15px;">Tổng tiền: <span id="total-price" style="color: #e50914; font-size: 20px; font-weight: bold;">0đ</span></p>
                    <button id="btn-confirm" class="btn btn-danger btn-block" style="background: #e50914; height: 45px; font-weight: bold;" disabled>
                        <i class="fas fa-ticket-alt"></i> XÁC NHẬN ĐẶT VÉ
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Sơ đồ ghế -->
        <div class="col-md-8">
            <h2 style="color: #fff; margin-bottom: 20px;">Chọn Ghế Ngồi</h2>
            
            <!-- Chú thích -->
            <div style="background: #1a1a1a; padding: 20px; border-radius: 10px; margin-bottom: 25px;">
                <div class="row text-center">
                    <div class="col-xs-4">
                        <div class="seat available" style="display: inline-block;"></div>
                        <span style="color: #aaa; margin-left: 10px;">Ghế trống</span>
                    </div>
                    <div class="col-xs-4">
                        <div class="seat selected" style="display: inline-block;"></div>
                        <span style="color: #aaa; margin-left: 10px;">Ghế đang chọn</span>
                    </div>
                    <div class="col-xs-4">
                        <div class="seat booked" style="display: inline-block;"></div>
                        <span style="color: #aaa; margin-left: 10px;">Ghế đã đặt</span>
                    </div>
                </div>
            </div>
            
            <!-- Màn hình -->
            <div class="screen" style="background: linear-gradient(to bottom, #444, #222); height: 10px; border-radius: 50%; margin: 30px auto; width: 80%; box-shadow: 0 3px 20px rgba(229, 9, 20, 0.5);"></div>
            <p style="text-align: center; color: #aaa; margin-bottom: 30px;">MÀN HÌNH</p>
            
            <!-- Ghế ngồi -->
            <div id="seat-map" style="background: #1a1a1a; padding: 30px; border-radius: 10px;">
                <?php
                $rows = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'];
                $seats_per_row = 10;
                
                foreach($rows as $row) {
                    echo '<div class="seat-row" style="margin-bottom: 15px; text-align: center;">';
                    echo '<span class="row-label" style="color: #e50914; font-weight: bold; margin-right: 15px; display: inline-block; width: 25px;">'.$row.'</span>';
                    
                    for($i = 1; $i <= $seats_per_row; $i++) {
                        $seat_id = $row . $i;
                        $is_booked = in_array($seat_id, $booked_seats);
                        $class = $is_booked ? 'seat booked' : 'seat available';
                        $disabled = $is_booked ? 'disabled' : '';
                        
                        echo '<button class="'.$class.'" data-seat="'.$seat_id.'" '.$disabled.'>'.$i.'</button>';
                        
                        if($i == 5) echo '<span style="display: inline-block; width: 30px;"></span>';
                    }
                    
                    echo '</div>';
                }
                ?>
            </div>
        </div>
    </div>
</div>

<style>
.seat {
    width: 40px;
    height: 40px;
    margin: 3px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 12px;
    transition: all 0.3s;
}

.seat.available {
    background: #2a2a2a;
    color: #fff;
}

.seat.available:hover {
    background: #3a3a3a;
    transform: scale(1.1);
}

.seat.selected {
    background: #e50914;
    color: #fff;
    transform: scale(1.1);
}

.seat.booked {
    background: #555;
    color: #888;
    cursor: not-allowed;
}

.seat-row {
    display: flex;
    justify-content: center;
    align-items: center;
}
</style>

<script>
const seatPrice = <?php echo $screening['price'];?>;
let selectedSeats = [];

document.querySelectorAll('.seat.available').forEach(seat => {
    seat.addEventListener('click', function() {
        const seatId = this.getAttribute('data-seat');
        
        if(this.classList.contains('selected')) {
            this.classList.remove('selected');
            selectedSeats = selectedSeats.filter(s => s !== seatId);
        } else {
            this.classList.add('selected');
            selectedSeats.push(seatId);
        }
        
        updateBookingInfo();
    });
});

function updateBookingInfo() {
    const count = selectedSeats.length;
    const total = count * seatPrice;
    
    document.getElementById('seat-count').textContent = count;
    document.getElementById('total-price').textContent = total.toLocaleString('vi-VN') + 'đ';
    document.getElementById('btn-confirm').disabled = count === 0;
}

// Check seat availability before submitting
function checkSeatAvailability() {
    return fetch('check_seat_availability.php?screening_id=<?php echo $screening_id;?>')
        .then(response => response.json())
        .then(data => {
            if(!data.success) {
                throw new Error('Không thể kiểm tra tình trạng ghế');
            }
            
            // Check if any selected seats are now booked
            const takenSeats = selectedSeats.filter(seat => data.booked_seats.includes(seat));
            if(takenSeats.length > 0) {
                return {
                    available: false,
                    takenSeats: takenSeats
                };
            }
            
            return { available: true };
        });
}

document.getElementById('btn-confirm').addEventListener('click', function() {
    if(selectedSeats.length === 0) {
        alert('Vui lòng chọn ít nhất 1 ghế!');
        return;
    }
    
    if(confirm('Xác nhận đặt ' + selectedSeats.length + ' ghế: ' + selectedSeats.join(', ') + '?')) {
        // Disable button to prevent double-click
        this.disabled = true;
        this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang xử lý...';
        
        // Check seat availability before submitting
        checkSeatAvailability()
            .then(result => {
                if(!result.available) {
                    alert('Ghế ' + result.takenSeats.join(', ') + ' đã được đặt bởi người dùng khác! Vui lòng chọn ghế khác.');
                    // Remove taken seats from selection
                    result.takenSeats.forEach(seat => {
                        const seatElement = document.querySelector(`[data-seat="${seat}"]`);
                        if(seatElement) {
                            seatElement.classList.remove('selected');
                            seatElement.classList.add('booked');
                            seatElement.style.cursor = 'not-allowed';
                        }
                        selectedSeats = selectedSeats.filter(s => s !== seat);
                    });
                    updateBookingInfo();
                    this.disabled = false;
                    this.innerHTML = '<i class="fas fa-ticket-alt"></i> XÁC NHẬN ĐẶT VÉ';
                    return;
                }
                
                // Submit booking via AJAX
                const formData = new FormData();
                formData.append('screening_id', '<?php echo $screening_id;?>');
                formData.append('seats', selectedSeats.join(','));
                formData.append('total_amount', selectedSeats.length * seatPrice);
                
                fetch('process_booking.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if(data.success) {
                        alert('Đặt vé thành công! Mã đặt vé: #' + data.data.booking_id);
                        window.location = 'booking_history.php';
                    } else {
                        if(data.error_code === 'seats_taken' || data.error_code === 'seat_taken') {
                            alert(data.message);
                            // Mark taken seats as booked
                            if(data.data && data.data.taken_seats) {
                                data.data.taken_seats.forEach(seat => {
                                    const seatElement = document.querySelector(`[data-seat="${seat}"]`);
                                    if(seatElement) {
                                        seatElement.classList.remove('selected');
                                        seatElement.classList.add('booked');
                                        seatElement.style.cursor = 'not-allowed';
                                    }
                                    selectedSeats = selectedSeats.filter(s => s !== seat);
                                });
                                updateBookingInfo();
                            }
                        } else if(data.error_code === 'not_authenticated') {
                            alert(data.message);
                            window.location = 'login.php';
                        } else {
                            alert(data.message);
                        }
                        this.disabled = false;
                        this.innerHTML = '<i class="fas fa-ticket-alt"></i> XÁC NHẬN ĐẶT VÉ';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Đã xảy ra lỗi! Vui lòng thử lại.');
                    this.disabled = false;
                    this.innerHTML = '<i class="fas fa-ticket-alt"></i> XÁC NHẬN ĐẶT VÉ';
                });
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Không thể kiểm tra tình trạng ghế! Vui lòng thử lại.');
                this.disabled = false;
                this.innerHTML = '<i class="fas fa-ticket-alt"></i> XÁC NHẬN ĐẶT VÉ';
            });
    }
});
</script>

<?php include('footer.php'); ?>