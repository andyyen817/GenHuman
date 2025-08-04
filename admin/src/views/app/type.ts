export const AppList = [
    { label: '口播文案', value: 'oralCopy' },
    { label: '人脸融合', value: 'facialFusion' },
    { label: '画质增强', value: 'photoHD' },
    { label: '老照片修复', value: 'oldPhotoRestoration' },
    { label: '人物换发型', value: 'hairStyle' },
]

export const ComponentList = [
    { label: '输入框', value: 'input' },
    { label: '单选框', value: 'radio' },
    { label: '多选框', value: 'checkbox' },
    { label: '文本域', value: 'textarea' },
]


export function getLabel(value: string, type: string = 'AppList'): string {
    console.log(value)
    const List = type === 'AppList' ? AppList : ComponentList;
    const item = List.find(item => item.value === value);
    return item ? item.label : '';
}