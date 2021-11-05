# Web restaurant server
## 1. Thông tin API:
+ Thông tin lúc gửi đi:
  + method: **GET** hoặc **POST**.
  + url: đường link kết nối với server.
  + body: là một object kèm theo các trường tương ứng.
  + header: gửi đi **token** (nếu cần).
+ Tín hiệu nhận về có 2 loại:
  + **Success**: có dạng như sau:
    ```
    {
      "response" : object | string | array,
      "status" : 200,
    }
    ```
  + **Failure**: có dạng như sau:
    ```
    {
      "message" : string,
      "status" : 40x,
    }
    ```

## 2. Chi tiết về các API:
+ Phía khách:
  + Lấy thông tin tất cả các blog:
    + method: GET.
    + url: `/blogs`.
    + body: {}.
    + header: {}.
  + Lấy thông tin chi tiết của blog:
    + method: GET.
    + url: `/blogs/{id}`.
    + body: {}.
    + header: {}.
  + Lấy danh sách các món ăn:
    + method: GET.
    + url: `/menu`.
    + body: {}.
    + header: {}.
  + Đặt bàn:
    + method: POST.
    + url: `/reservation`
    + body: { description: string, NoP: number [1, 30] }
    + header: {}.
+ Phía người dùng và quản lý:
  + Login:
    + method: POST.
    + url: `/auth/login`.
    + body: { username, password }.
    + header: {}.
  + Register:
    + method: POST.
    + url: `/auth/register`.
    + body: { username, password, password, phoneNumber, email, avatar }.
    + header: {}.

>> Khi người dùng đăng nhập hoặc đăng ký thành công. Thông tin trả về (response) có chứa token. Phía client cần phải lưu thông tin này lại, để có thể thực hiện các quyền cần thiết.
+ Phía người dùng: sau khi đã đăng nhập sẽ được cấp 1 token. Mọi thao tác của người dùng đều phải có token kèm theo trong header.
  + Tạo comment mới:
    + method: POST.
    + url: `/comment/create`.
    + body: { blogId: number, description: string, userId: number }
    + header: token.
  + Xóa comment:
    + method: POST.
    + url: `/comment/delete/{comment_id}`.
    + body: {}.
    + header: token.
  + Update profile:
    + method: POST.
    + url: `/auth/update_profile`.
    + body: { username, email, phoneNumber, avatar, userId }.
    + header: token.
  + Update password:
    + method: POST.
    + url: `/auth/update_password`.
    + body: { old_password, new_password, verify_password }.
    + header: token.
+ Phía Admin:
  + Create dish:
    + method: POST.
    + url: `/admin/create_dish`.
    + body: { name, description, image }.
    + header: token.
  + Delete dish:
    + method: POST.
    + url: `/admin/delete_dish/{dish_id}`.
    + body: {}.
    + header: token.
  + Create blog:
    + method: POST.
    + url: `/admin/create_blog`.
    + body: { title, content, image }.
    + header: token.
  + Update blog:
    + method: POST.
    + url: `/admin/update_blog/{blog_id}`.
    + body: { title, content, image }.
    + header: token.
  + Delete blog:
    + method: POST.
    + url: `/admin/delete_blog/{blog_id}`.
    + body: {}.
    + header: token.
  + Lấy tất cả user:
    + method: GET.
    + url: `/admin/users`.
    + body: {}.
    + header: token.
  + Delete user:
    + method: POST.
    + url: `/admin/delete_user/{user_id}`.
    + body: {}.
    + header: token.
  + Delete comment:
    + method: POST.
    + url: `/admin/delete_comment/{comment_id}`.
    + body: {}.
    + header: token.