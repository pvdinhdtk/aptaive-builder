// types/ui.ts
import {
    CategoryGridSection,
    IconItem,
    PostListSection,
    ProductListSection,
    SliderItem,
    SliderSection
} from "../models/config";

export type EditorBase = {
    id: string;
};

/* ===== ICON ITEM (EDITOR) ===== */
export type EditorIconItem = EditorBase & IconItem;

/* ===== Slider ITEM (EDITOR) ===== */
export type EditorSliderItem = EditorBase & SliderItem;

/* ===== SECTIONS ===== */
export type EditorSliderSection = EditorBase &
    Omit<SliderSection, "items"> & {
        items: EditorSliderItem[];
    };

export type EditorCategoryGridSection = EditorBase &
    Omit<CategoryGridSection, "items"> & {
        items?: IconItem[]; // ✅ ĐÚNG
    };

export type EditorProductListSection = EditorBase & ProductListSection;
export type EditorPostListSection = EditorBase & PostListSection;

export type EditorHomeLayout =
    | EditorSliderSection
    | EditorCategoryGridSection
    | EditorProductListSection
    | EditorPostListSection;
