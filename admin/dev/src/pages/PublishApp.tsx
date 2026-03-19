import { BuildSidebar } from './AppSettings';

export default function PublishApp() {
    return (
        <div className="aptaive-publish-page">
            <div className="aptaive-publish-copy">
                <span className="aptaive-publish-kicker">Build App</span>
                <h1>Build App</h1>
                <p>
                    Làm theo các bước bên dưới để hoàn tất cấu hình, gửi yêu
                    cầu build và nhận file ứng dụng từ hệ thống Aptaive.
                </p>
            </div>

            <BuildSidebar />
        </div>
    );
}
