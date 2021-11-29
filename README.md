# Web restaurant server

## 1. Lưu ý khi chạy:

- Trong file db.php, cần đổi các trường hostname, username, password, database trong câu lệnh mysqli_connect về đúng với máy chủ đang chạy XAMPP

## 2. Thông tin API:

- Thông tin lúc gửi đi:
  - method: **GET** hoặc **POST**.
  - url: đường link kết nối với server.
  - body: là một object kèm theo các trường tương ứng.
  - header: gửi đi **token** (nếu cần).
- Tín hiệu nhận về có 2 loại:
  - **Success**: có dạng như sau:
    ```
    {
      "response" : object | string | array,
      "status" : 200,
    }
    ```
  - **Failure**: có dạng như sau:
    ```
    {
      "message" : string,
      "status" : 40x,
    }
    ```
- Một số tín hiệu trả về đặc biệt:
  - `404 - Not found`: kiểm tra lại method hoặc đường link truy cập api.
  - `Server or databse is error`: kiểm tra lại server hoặc db có đang được chạy hay không. Hoặc thao tác trên dữ liệu không hợp lệ (dữ liệu đã bị xóa).

## 3. Chi tiết về các API:

- Phía khách:
  - Lấy thông tin tất cả các blog:
    - request:
      - method: GET.
      - url: `/blogs`.
      - body: {}.
      - header: {}.
    - response: một array các **blog**.
  - Lấy thông tin chi tiết của blog:
    - request:
      - method: GET.
      - url: `/blog/{id}`.
      - body: {}.
      - header: {}.
    - response:
      - blog: chi tiết về blog đó.
      - comments: danh sách comment
  - Lấy danh sách các món ăn:
    - request:
      - method: GET.
      - url: `/menu`.
      - body: {}.
      - header: {}.
    - response:
      - một array các **dish**.
  - Đặt bàn:
    - request:
      - method: POST.
      - url: `/reservation`
      - body:
        ```
        {
            description,
            NoP: [1, 30],
            date: định dạng yyyy-mm-dd.
            time: định dạng hh:mm:ss.
            name
            email
            phoneNumber: length = 10
        }
        ```
      - header: {}.
    - response: một object chứa các thông tin đã điền.
- Phía người dùng và quản lý:
  - Login:
    - request:
      - method: POST.
      - url: `/auth/login`.
      - body: { username, password }.
      - header: {}.
    - response:
      - user: object chứa thông tin user.
      - token
  - Register: + request: + method: POST. + url: `/auth/register`. + body: { username, password, verify_password, phoneNumber : length = 10, email, avatar }. + header: {}. + response: + user: object chứa thông tin user đã đăng ký. + token
    > > Khi người dùng đăng nhập hoặc đăng ký thành công. Thông tin trả về (response) có chứa token. Phía client cần phải lưu thông tin này lại, để có thể thực hiện các hành động cần thiết.
- Phía người dùng: Mọi thao tác của người dùng đều phải có token kèm theo trong **header**. Dưới định dạng sau:
  ```
      'Bear-Token': token
  ```
  - Tạo comment mới:
    - request:
      - method: POST.
      - url: `/comment/create`.
      - body: { blogId, description, userId }
      - header: token.
    - response: object chứa thông tin comment đã tạo.
  - Xóa comment:
    - request:
      - method: POST.
      - url: `/comment/delete/{comment_id}`.
      - body: {blogId}.
      - header: token.
    - response: thông báo xóa thành công.
  - Update profile:
    - request:
      - method: POST.
      - url: `/auth/update_profile`.
      - body: { username, email, phoneNumber, avatar, userId }.
      - header: token.
    - response: object chứa thông tin user.
  - Update password:
    - request:
      - method: POST.
      - url: `/auth/update_password`.
      - body: { old_password, new_password, verify_password }.
      - header: token.
    - response: thông báo **login** lại.
- Phía Admin:
  - Get dish detail:
    - request:
      - method: GET.
      - url: `/dish/{id}`.
      - body: {}.
      - header: {}.
    - response: object chứa thông tin món ăn.
  - Create dish:
    - request:
      - method: POST.
      - url: `/admin/create_dish`.
      - body: { name, description, image }.
      - header: token.
    - response: object về dish đã tạo.
  - Update dish:
    - request:
      - method: POST.
      - url: `/admin/update_dish/{dish_id}`.
      - body: {}.
      - header: token.
    - response: object về dish đã cập nhật.
  - Delete dish:
    - request:
      - method: POST.
      - url: `/admin/delete_dish/{dish_id}`.
      - body: {}.
      - header: token.
    - response: thông báo xóa thành công.
  - Create blog:
    - request:
      - method: POST.
      - url: `/admin/create_blog`.
      - body: { title, content, image }.
      - header: token.
    - response: object chứa thông tin blog đã tạo.
  - Update blog:
    - request:
      - method: POST.
      - url: `/admin/update_blog/{blog_id}`.
      - body: { title, content, image }.
      - header: token.
    - response:
      - blog: object chứa thông tin về blog.
      - comments: danh sách các comment.
  - Delete blog:
    - request:
      - method: POST.
      - url: `/admin/delete_blog/{blog_id}`.
      - body: {}.
      - header: token.
    - response: thông báo xóa thành công.
  - Lấy tất cả user:
    - request:
      - method: GET.
      - url: `/admin/users`.
      - body: {}.
      - header: token.
    - response: một array các **user**.
  - Delete user:
    - request:
      - method: POST.
      - url: `/admin/delete_user/{user_id}`.
      - body: {}.
      - header: token.
    - response: thông báo thành công.
  - Delete comment:
    - request:
      - method: POST.
      - url: `/admin/delete_comment/{comment_id}`.
      - body: {}.
      - header: token.
    - response: thông báo thành công.
