export const userSettings = {
    // Layout: localStorage.getItem('data-layout') ?? 'vertical', // vertical | horizontal
    SidebarType: "full", // full | mini-sidebar
    BoxedLayout: false, // true | false
    Direction: "ltr", // ltr | rtl
    Theme: localStorage.getItem("hs_theme") ?? "light", // light | dark
    //   Theme: "light", // light | dark
    ColorTheme: localStorage.getItem("data-color-theme") ?? "Blue_Theme", // Blue_Theme | Aqua_Theme | Purple_Theme | Green_Theme | Cyan_Theme | Orange_Theme
    cardBorder: false, // true | false
};
