import {
    Button,
    Card,
    CardBody,
    CardHeader,
    Spinner,
    TextControl,
} from '@wordpress/components';

import { ColorField } from '../components/ColorField';
import { CardImageField } from '../components/ImageField';
import { useConfigStore } from '../store/configStore';

export default function AppSettings() {
    const { config, updateApp, loading, save } = useConfigStore();
    if (loading || !config) return <Spinner />;

    const app = config!.app;

    return (
        <>
            <div className="aptaive-cards">

                <Card>
                    <CardHeader>
                        <strong>General</strong>
                    </CardHeader>
                    <CardBody>
                        <TextControl
                            label="App Name"
                            value={app.appName}
                            help="Tên hiển thị của ứng dụng trên màn hình và trong app store"
                            onChange={(v) => updateApp('appName', v)}
                        />

                        <TextControl
                            label="Application ID"
                            help="Định danh duy nhất của ứng dụng (ví dụ: com.example.myapp)"
                            value={app.applicationId}
                            onChange={(v) => updateApp('applicationId', v)}
                        />

                        <TextControl
                            label="Phiên bản"
                            help="Phiên bản của ứng dụng (ví dụ: 1.0.0)"
                            value={app.version}
                            onChange={(v) => updateApp('version', v)}
                        />

                        <TextControl
                            label="Phiên bản cập nhật"
                            help="Chỉ nhập khi bạn có yêu cầu nâng cấp lại ứng dụng với phiên bản mới"
                            value={app.versionUpdate}
                            onChange={(v) => updateApp('versionUpdate', v)}
                        />

                        <TextControl
                            label="Link Download"
                            help="Link download file APK hoặc Google Play người dùng sẽ tải file này khi app thông báo có phiên bản mới hơn"
                            value={app.download.android}
                            onChange={(v) =>
                                updateApp('download', {
                                    ...app.download,
                                    android: v,
                                })
                            }
                        />
                    </CardBody>
                </Card>

                <Card>
                    <CardHeader>
                        <strong>Branding</strong>
                    </CardHeader>
                    <CardBody>
                        <CardImageField
                            label="Logo"
                            value={app.logo}
                            onChange={(v) => updateApp('logo', v)}
                        />

                        <CardImageField
                            label="App Icon"
                            value={app.icon}
                            onChange={(v) => updateApp('icon', v)}
                            help="Icon đại diện cho ứng dụng. Khuyến nghị ảnh vuông (1024×1024px)"
                        />
                    </CardBody>
                </Card>

                <Card>
                    <CardHeader>
                        <strong>Colors</strong>
                    </CardHeader>
                    <CardBody>
                        <ColorField
                            label="Primary Color"
                            value={app.primaryColor}
                            onChange={(v) => updateApp('primaryColor', v)}
                        />

                        <ColorField
                            label="Secondary Color"
                            value={app.secondaryColor}
                            onChange={(v) => updateApp('secondaryColor', v)}
                        />

                        <ColorField
                            label="Text Primary Color"
                            value={app.textPrimaryColor}
                            onChange={(v) => updateApp('textPrimaryColor', v)}
                        />

                        <ColorField
                            label="Text Secondary Color"
                            value={app.textSecondaryColor}
                            onChange={(v) => updateApp('textSecondaryColor', v)}
                        />
                    </CardBody>
                </Card>



            </div>
            <Button
                variant="primary"
                onClick={save}
                style={{ marginTop: 16 }}
            >
                Save Changes
            </Button>
        </>
    );
}
