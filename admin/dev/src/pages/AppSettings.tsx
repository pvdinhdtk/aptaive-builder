import { Button, Card, CardBody, CardHeader, TextControl } from "@wordpress/components";

import Skeleton from "react-loading-skeleton";
import { ColorField } from "../components/ColorField";
import { CardImageField } from "../components/ImageField";
import { useConfigStore } from "../store/configStore";

const BUILD_STEPS = [
  {
    number: "01",
    title: "Thiết lập ứng dụng và giao diện",
    description: "Cấu hình app và màu sắc, menu, bố cục trang chủ và trải nghiệm hiển thị.",
  },
  {
    number: "02",
    title: "Gửi yêu cầu build",
    description: "Truy cập hệ thống Aptaive, chọn nền tảng cần build và hoàn tất thanh toán.",
  },
  {
    number: "03",
    title: "Nhận file ứng dụng",
    description: "Nhận file build để phát hành lên store hoặc bàn giao cho khách hàng.",
  },
];

export default function AppSettings() {
  const { config, updateApp, loading, save } = useConfigStore();
  if (loading || !config) {
    return (
      <>
        <div className="aptaive-cards">
          <SettingsCardSkeleton title="General" fields={5} />
          <SettingsCardSkeleton title="Branding" fields={2} />
          <SettingsCardSkeleton title="Colors" fields={4} />
        </div>

        <Skeleton height={36} width={140} style={{ marginTop: 16 }} />
      </>
    );
  }

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
              onChange={(v) => updateApp("appName", v)}
            />

            <TextControl
              label="Application ID"
              help="Định danh duy nhất của ứng dụng (ví dụ: com.example.myapp)"
              value={app.applicationId}
              onChange={(v) => updateApp("applicationId", v)}
            />

            <TextControl
              label="Link Download Android"
              help="Link APK hoặc Google Play dùng khi app Android cần cập nhật"
              value={app.download.android}
              onChange={(v) =>
                updateApp("download", {
                  ...app.download,
                  android: v,
                })
              }
            />

            <TextControl
              label="Link Download iOS"
              help="Link App Store hoặc trang tải dành cho thiết bị iOS"
              value={app.download.ios}
              onChange={(v) =>
                updateApp("download", {
                  ...app.download,
                  ios: v,
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
            <CardImageField label="Logo" value={app.logo} onChange={(v) => updateApp("logo", v)} />

            <CardImageField
              label="App Icon"
              value={app.icon}
              onChange={(v) => updateApp("icon", v)}
              help="Icon đại diện cho ứng dụng. Khuyến nghị ảnh vuông (1024×1024px)"
            />
          </CardBody>
        </Card>

        <Card>
          <CardHeader>
            <strong>Colors</strong>
          </CardHeader>
          <CardBody>
            <ColorField label="Primary Color" value={app.primaryColor} onChange={(v) => updateApp("primaryColor", v)} />

            <ColorField label="Secondary Color" value={app.secondaryColor} onChange={(v) => updateApp("secondaryColor", v)} />

            <ColorField label="Text Primary Color" value={app.textPrimaryColor} onChange={(v) => updateApp("textPrimaryColor", v)} />

            <ColorField label="Text Secondary Color" value={app.textSecondaryColor} onChange={(v) => updateApp("textSecondaryColor", v)} />
          </CardBody>
        </Card>
      </div>
      <Button variant="primary" onClick={save} style={{ marginTop: 16 }}>
        Save Changes
      </Button>
    </>
  );
}

export function BuildSidebar() {
  return (
    <div className="aptaive-build-panel">
      <div className="aptaive-build-hero">
        <span className="aptaive-build-badge">Aptaive Builder</span>
        <h3>Bắt đầu build ứng dụng</h3>
        <p>Hoàn tất cấu hình trong plugin, sau đó chuyển sang hệ thống Aptaive để gửi yêu cầu build và nhận file ứng dụng.</p>
      </div>

      <div className="aptaive-build-steps">
        {BUILD_STEPS.map((step) => (
          <div key={step.number} className="aptaive-build-step">
            <div className="aptaive-build-step-number">{step.number}</div>

            <div className="aptaive-build-step-content">
              <strong>{step.title}</strong>
              <p>{step.description}</p>
            </div>
          </div>
        ))}
      </div>

      <a className="aptaive-build-cta" href="https://app.taive.net/" target="_blank" rel="noreferrer">
        Tiến hành build
      </a>
    </div>
  );
}

function SettingsCardSkeleton({ title, fields = 3 }: { title: string; fields?: number }) {
  return (
    <Card>
      <CardHeader>
        <strong>
          <Skeleton width={120} height={16} />
        </strong>
      </CardHeader>

      <CardBody>
        {Array.from({ length: fields }).map((_, i) => (
          <FieldSkeleton key={i} />
        ))}
      </CardBody>
    </Card>
  );
}

function FieldSkeleton() {
  return (
    <div style={{ marginBottom: 16 }}>
      <Skeleton height={14} width="30%" style={{ marginBottom: 6 }} />
      <Skeleton height={32} />
    </div>
  );
}
