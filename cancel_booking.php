<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';

if(!isLoggedIn()) {
    redirect('login.php');
}

if(isset($_GET['id'])) {
    $booking_id = $_GET['id'];
    
    // Получаем данные пользователя
    $stmt = $pdo->prepare("SELECT id, phone FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
    
    if($user) {
        // Проверяем, что запись принадлежит пользователю (по телефону)
        $stmt = $pdo->prepare("SELECT id, client_phone, schedule_id FROM bookings WHERE id = ? AND status = 'active'");
        $stmt->execute([$booking_id]);
        $booking = $stmt->fetch();
        
        if($booking && $booking['client_phone'] == $user['phone']) {
            // Начинаем транзакцию
            $pdo->beginTransaction();
            
            try {
                // Проверяем, было ли списание за это занятие
                $stmt = $pdo->prepare("SELECT * FROM membership_logs WHERE booking_id = ? AND action = 'booking'");
                $stmt->execute([$booking_id]);
                $log = $stmt->fetch();
                
                if($log) {
                    // Если было списание - возвращаем занятие
                    $stmt = $pdo->prepare("
                        UPDATE memberships 
                        SET lessons_left = lessons_left + 1 
                        WHERE user_id = ? AND lessons_left < 999
                    ");
                    $stmt->execute([$user['id']]);
                    
                    // Записываем в лог возврат
                    $stmt = $pdo->prepare("
                        INSERT INTO membership_logs (user_id, action, lessons_before, lessons_after, lessons_changed, booking_id, description) 
                        VALUES (?, 'refund', ?, ?, 1, ?, 'Возврат при отмене')
                    ");
                    $stmt->execute([$user['id'], $log['lessons_before'], $log['lessons_before'] + 1, $booking_id]);
                }
                
                // Отменяем запись
                $stmt = $pdo->prepare("UPDATE bookings SET status = 'cancelled' WHERE id = ?");
                $stmt->execute([$booking_id]);
                
                $pdo->commit();
                
            } catch(Exception $e) {
                $pdo->rollBack();
                error_log("Cancel error: " . $e->getMessage());
            }
        }
    }
}

redirect('cabinet.php');
?>