<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang chủ - Tin tức</title>
    <link rel="stylesheet" href="./css/index.css">
</head>
<body>
    <h1>Đường Kênh News</h1>
        <!-- Nếu người dùng chưa đăng nhập -->
    <div class="btn-container">
        <!-- Nút Đăng nhập -->
        <a href="login.php" class="btn btn-info">Đăng nhập</a>

        <!-- Nút Đăng ký -->
        <a href="signup.php" class="btn">Đăng ký</a>
    </div>
    <div class="container">
        <?php
        // Giả sử danh sách tin tức được lấy từ cơ sở dữ liệu
        $news = [
            ["title" => "Trần Văn Chiến", "content" => "
                                                            <em>Nhiệt tình, tự tin và thân thiện</em> là ba từ tôi muốn mô tả về bản thân. 
                                                            Sự nhiệt tình của tôi đối với công việc của một lập trình viên cho phép tôi duy 
                                                            trì động lực trong công việc và nhận thấy tầm quan trọng những gì tôi 
                                                            đang làm. <b><em>Sự tự tin giúp tôi nhận ra khả năng của mình</em></b> . Tôi cũng 
                                                            nghĩ mình là một người <b>cực kỳ thân thiện</b>, vì tôi thích 
                                                            tương tác với khách hàng cũng như đồng nghiệp của mình mỗi ngày.
                                                        ", 
                                                        "image" => "img/avt1.jpg"],
            ["title" => "Phạm Trần Anh", "content" => "
                                                        Tôi là người <b>có tổ chức, kiên nhẫn và đáng tin cậy.</b> Tôi tự hào về <em>kỹ năng quản lý 
                                                        thời gian của mình</em> và khả năng đáp ứng những lịch trình bất ngờ. <em><b>Khi tôi gặp 
                                                        khó khăn, tôi không bỏ cuộc.</b></em> Thay vào đó, tôi sẽ kiên nhẫn và tiếp tục 
                                                        làm việc cho đến khi tôi đạt được kết quả. Cuối cùng, tôi chọn từ đáng tin 
                                                        cậy vì tôi luôn tuân thủ các cam kết của mình. Nếu tôi tình nguyện làm 
                                                        điều gì đó, bạn có thể đảm bảo rằng <b>tôi sẽ hoàn thành nó với khả năng 
                                                        tốt nhất của mình.</b>
                                                    ", 
                                                        "image" => "img/avt2.jpg"],
            ["title" => "Võ Quốc Việt", "content" => "
                                                        Từ đầu tiên tôi dùng để mô tả về bản thân là <b>dễ gần</b>. Thực tế, khi gặp gỡ khách 
                                                        hàng tôi luôn cố gắng hết sức để họ cảm thấy thoải mái trong quá trình 
                                                        hỗ trợ và giải đáp thắc mắc cho họ. Tôi cũng là một người khá tinh ý. 
                                                        <b><em>Tôi cố gắng để ý tới những chi tiết nhỏ mà người khác có thể không nhận 
                                                        thấy.</em></b>
                                                        Cuối cùng, tôi là người ham học hỏi. Trong công việc, tôi thường hay học 
                                                        hỏi các cách làm việc của đồng nghiệp nếu cảm thấy cách làm của mình không 
                                                        thực sự tốt. <b>Tôi thích thử các cách tiếp cận và rèn luyện kỹ năng mới để phục 
                                                        vụ khách hàng được tốt hơn.</b>
                                                    ", 
                                                        "image" => "img/avt3.jpg"],
            ["title" => "Lê Thiện Nguyên", "content" => "
                                                            Tính cách của tôi có thể được mô tả bằng ba từ: <i><b>chủ động, kiên nhẫn 
                                                            và sáng tạo.</b></i> Tôi là người luôn chủ động trong mọi tình huống, 
                                                            không ngần ngại tiếp cận và giải quyết vấn đề. <b>Khả năng kiên nhẫn 
                                                            của tôi giúp tôi vượt qua những thử thách</b> một cách <u>bền bỉ</u> và <u>không 
                                                            từ bỏ dễ dàng.</u> Đồng thời, với <b>tinh thần sáng tạo</b>, tôi thích 
                                                            tìm kiếm cách tiếp cận vấn đề từ các góc độ mới, <i>tạo ra những 
                                                            giải pháp độc đáo và hiệu quả.</i> Những phẩm chất 
                                                            này đã giúp tôi không chỉ tự 
                                                            phát triển mà còn đóng góp tích cực trong mọi môi trường làm việc.
                                                        ", 
                                                        "image" => "img/avt4.jpg"],
            ["title" => "Tô Hoàng Vũ", "content" => "
                                                        Tôi là <u>người chủ động</u>, <u>kiên nhẫn</u> và <u>sáng tạo</u>. Tôi luôn tích 
                                                        cực tiếp cận và giải quyết vấn đề, <b>không bao giờ từ bỏ trước những thử 
                                                        thách</b>. Với tinh thần sáng tạo, tôi luôn tìm 
                                                        cách tiếp cận vấn đề từ góc độ mới, tạo ra những giải 
                                                        pháp độc đáo và hiệu quả.", 
                                                    "image" => "img/avt5.jpg"],
            ["title" => "Nguyễn Hữu Phúc Thịnh", "content" => "
                                                                <b>Tính cách kiên định</b> của tôi thường được <u>thể hiện qua sự 
                                                                cam kết</u> và <u>sự kiên trì</u> trong mọi công việc. Dù gặp phải khó 
                                                                khăn và thách thức, <b><i>tôi luôn 
                                                                cố gắng và không bao giờ từ bỏ mục tiêu của mình.</i></b> Tôi 
                                                                tin rằng bằng sự kiên định và nỗ lực, mọi khó khăn đều có 
                                                                thể vượt qua.
                                                            ", 
                                                            "image" => "img/avt6.jpg"],
            ["title" => "Nguyễn Học Nam", "content" => "
                                                            Tôi có thể mô tả bản thân bằng ba từ: <u>nhiệt huyết</u>, <u>linh 
                                                            hoạt</u> và <u>tận tụy</u>. Sự nhiệt huyết luôn là động lực lớn nhất 
                                                            của tôi trong mọi hoạt động. Tôi luôn đam mê và tận hưởng mỗi khía cạnh 
                                                            của cuộc sống và công việc. Sự nhiệt huyết giúp <b>tôi không ngừng 
                                                            khám phá</b>, <b>học hỏi</b> và <b>phát triển bản thân</b>, cũng như 
                                                            tạo ra sự <b>lan tỏa tích cực trong môi trường xung quanh.</b>
                                                        ", 
                                                        "image" => "img/avt7.jpg"],
            ["title" => "Nguyễn Thị Quỳnh Nga", "content" => "
                                                                <b>Sự chăm chỉ là nguồn động lực không ngừng của tôi.</b> Tôi tin 
                                                                rằng không có công việc nào là quá khó khăn nếu ta đặt vào đó đủ sự 
                                                                cống hiến và chăm chỉ. Tôi luôn cam kết làm việc hết mình, <b>không 
                                                                ngừng học hỏi và hoàn thiện bản thân để đạt được mục tiêu</b> đề ra. 
                                                                Đối với tôi, sự chăm chỉ không chỉ là một phẩm chất cá nhân mà còn 
                                                                là <u>chìa khóa dẫn đến thành công và sự phát triển trong cuộc sống 
                                                                và sự nghiệp.</u>
                                                            ", 
                                                            "image" => "img/avt8.jpg"],
            ["title" => "Lê Thuỳ Linh", "content" => "
                                                        <b>Niềm đam mê với nhảy nhót</b> là <u>nguồn cảm hứng không ngừng của 
                                                        tôi</u>, và <b>tôi luôn tích cực tìm kiếm cơ hội để thể hiện niềm đam 
                                                        mê này.</b> <i>Bằng sự quyết tâm và lòng say mê không ngừng, tôi không 
                                                        chỉ tìm cách phát triển kỹ năng nhảy mà còn lan tỏa sự nhiệt huyết đến 
                                                        những người xung quanh.</i> Tôi luôn đặt ra những mục tiêu rõ ràng và 
                                                        làm việc chăm chỉ để đạt được chúng, không ngừng khích lệ bản thân và 
                                                        người khác. <b>Sự tích cực của tôi không chỉ giới hạn trong lĩnh vực nhảy 
                                                        nhót mà còn lan tỏa vào mọi khía cạnh của cuộc sống, giúp tôi vượt qua mọi 
                                                        thách thức và đạt được những thành công đáng kể.</b>
                                                    ", 
                                                    "image" => "img/avt9.jpg"],
            // Thêm các tin tức khác tương tự ở đây
        ];
        
        // Hiển thị tin tức
        foreach ($news as $article) {
            echo '<div class="article">';
            echo '<span class="avatar"><img src="' . $article['image'] . '" alt="Avatar"></span>';
            echo '<div class="content">';
            echo '<h2>' . $article['title'] . '</h2>';
            echo '<p>' . $article['content'] . '</p>';
            echo '</div>'; // Kết thúc nội dung tin tức
            echo '</div>'; // Kết thúc bài báo
        }
        ?>
    </div>
</body>
</html>
