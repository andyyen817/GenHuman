export interface BaseInterface {
    adapter: string
    size: number
}
export interface PublicInterface {
    url: string
}

export interface OssFormInterface {
    AccessId: string
    AccessSecret: string
    Bucket: string
    Endpoint: string
    url: string
    PrivateType: string | number
}

export interface CosFormInterface {
    Appid: string
    SecretId: string
    SecretKey: string
    Region: string
    Bucket: string
    url: string
    PrivateType: string | number
}

export interface SiteFormInterface {
    webName: string
    webLogo: string | any
    webUrl: string
}

export interface CommonFormInterface {
    avatar: string | any
    headerImg: string | any
}

export interface CustomerFormInterface {
    customerType: string | number //客服类型
    customerCorpId: string //企业客服ID
    customerUrl: string //企业客服链接
    customerQrcode: string | any //企业客服二维码
}


export interface WechatPayV3 {
    mchId: string
    paySignKey: string
    apiclientCert: string
    apiclientKey: string
}