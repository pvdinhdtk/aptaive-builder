/* ================= ENUMS ================= */

export enum CategoryGridMode {
    custom = "custom",
    postCategory = "postCategory",
    productCategory = "productCategory",
}

export enum TargetType {
    home = "home",
    account = "account",
    page = "page",
    post = "post",
    product = "product",
    postCategory = "postCategory",
    productCategory = "productCategory",
}

/* ================= BASIC MODELS ================= */

export type SliderItem = {
    image: string;
    targetType: TargetType;
    targetId?: number | null;
};

export type IconItem = {
    icon: string;
    label: string;
    targetType: TargetType;
    targetId?: number | null;
};

/* ================= HOME LAYOUT UNION ================= */

export type SliderSection = {
    type: "slider";
    title?: string | null;
    aspectRatio?: number;
    items: SliderItem[];
};

export type CategoryGridSection = {
    type: "categoryGrid";
    title?: string | null;
    mode: CategoryGridMode;
    rows: number; // default = 2 (UI xử lý)
    items?: IconItem[]; // chỉ dùng khi mode = custom
};

export type ProductListSection = {
    type: "productList";
    title?: string | null;
    columns: number; // default = 2
    limit?: number | null;
    categoryIds: number[];
};

export type PostListSection = {
    type: "postList";
    title?: string | null;
    columns: number; // default = 2
    limit?: number | null;
    categoryIds: number[];
};

export type HomeLayout =
    | SliderSection
    | CategoryGridSection
    | ProductListSection
    | PostListSection;

/* ================= LAYOUTS ================= */

export type BottomNavigationLayout = {
    items: IconItem[];
};

export type Layouts = {
    home: HomeLayout[];
    bottomNavigation: BottomNavigationLayout;
};

/* ================= APP ================= */

export type App = {
    appName: string;
    applicationId: string;
    download: Download;
    logo: string;
    icon: string;
    primaryColor: string;
    secondaryColor: string;
    textPrimaryColor: string;
    textSecondaryColor: string;
};

export type Download = {
    android: string;
    ios: string;
};

/* ================= ROOT CONFIG ================= */

export type Config = {
    schemaVersion: number;
    minAppVersion: string;
    app: App;
    layouts: Layouts;
};
