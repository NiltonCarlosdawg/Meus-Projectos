import { BsDiscord, BsFacebook, BsInstagram, BsPinterest, BsReddit, BsTelegram, BsTiktok, BsTwitterX, BsYoutube } from "react-icons/bs"
import { CgMore } from "react-icons/cg"

const Footer = () => {
  return (
    <footer className="flex justify-around bottom-0 px-[2%] mt-[40px]">
      <div>
        <h1 className="mb-[10px]">Community</h1>
        <ul className="flex items-center gap-[20px] cursor-pointer">
          <li><BsDiscord/></li>
          <li><BsTelegram/></li>
          <li><BsTiktok/></li>
          <li><BsFacebook/></li>
        </ul>
        <ul className="flex items-center gap-[20px] my-[20px] cursor-pointer">
          <li><BsTwitterX/></li>
          <li><BsReddit/></li>
          <li><BsInstagram/></li>
          <li><BsPinterest/></li>
        </ul>
        <div className="flex items-center gap-[20px]">
          <BsYoutube  className="cursor-pointer"/>
          <CgMore className="cursor-pointer"/>
        </div>
      </div>
      <div>
        <h1 className="mb-[10px]">About us</h1>
        <ul className="text-[gray] cursor-pointer">
          <li>About</li>
          <li>Careers</li>
          <li>Announcements</li>
          <li>News</li>
          <li>Press</li>
          <li>Legal</li>
          <li>Terms</li>
          <li>Privacy</li>
        </ul>
      </div>
      <div>
        <h1 className="mb-[10px]">Products</h1>
        <ul className="text-[gray] cursor-pointer">
          <li>Exchange</li>
          <li>Buy crypto</li>
          <li>Pay</li>
          <li>Academy</li>
          <li>Live</li>
          <li>Tax</li>
          <li>Gift card</li>
        </ul>
      </div>

        <div>
          <h1 className="mb-[10px]">Business</h1>
            <ul className="text-[gray] cursor-pointer">
              <li>P2P Merchant Application</li>
              <li>PSPro Merchant Application</li>
              <li>Listing Application</li>
              <li>Institucional & VIP Services</li>
              <li>Labs</li>
            </ul>
        </div>
        <div>
          <h1 className="mb-[10px]">Service</h1>
          <ul className="text-[gray] cursor-pointer">
            <li>Affiliate</li>
            <li>Referral</li>
            <li>OTC Trading</li>
            <li>Historical Market Data</li>
            <li>Proof of reserves</li>
          </ul>
        </div>
    </footer>
  )
}

export default Footer