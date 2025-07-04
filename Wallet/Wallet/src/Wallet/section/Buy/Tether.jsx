import { BiDollarCircle } from "react-icons/bi"
import { FaBtc } from "react-icons/fa"
import { FaEthereum } from "react-icons/fa6"
import { RiBnbFill, RiMoneyDollarCircleFill } from "react-icons/ri"
import { SiRipple, SiSolana } from "react-icons/si"
import { useSelector } from "react-redux"
import Graphic from "../Trade/Graphic"
import { aboutCoin } from "./object"
import CoinGraphic from "./CoinGraphic"

const Tether = () => {
  const coin = useSelector(state=>state.coin.coin)
  return (
    <div className="px-[4%]">
      <h1 className="text-[35px] font-[var] font-[500]">Tether {coin} Markets</h1>
      <div className="flex gap-5">
        <div>
          <div className="flex justify-between items-center">
            <div>
              <h2>{coin}/USD</h2>
              <div className="flex text-[30px]">
                {coin === "BTC"?<FaBtc/>:coin === "ETH"?<FaEthereum/>:coin === "BNB"?<RiBnbFill/>:coin === "XRP"?<SiRipple/>:coin === "SOL"?<SiSolana/>:<RiMoneyDollarCircleFill/>}<RiMoneyDollarCircleFill color="#f8d33a"/>
              </div>
   
            </div>
            <div className="font-bold text-[17px]">
              ${0.9999}
              <p className="text-[#2ebd85] ">+0.01%</p>
            </div>
          </div>
          <div>
            <CoinGraphic/>
          </div>
        </div>
        <div className="border-[#80808056] border-[1px] w-full rounded-[4px] px-[2%] py-[20px] h-[400px]" style={{overflowY:"scroll", scrollSnapType:"mandatory"}}>
          <div className="snap-center">
            <h2 className="text-[25px] font-bold">Markets</h2>
            <div>
              {
                aboutCoin.map((item, index)=>{

                  return(
                    <div key={index}>
                      <div className="flex items-center gap-40">
                        <div>
                          <div>
                            <p className="text-[#888da8] font-[500]">Popularity</p>
                            <p className="text-[20px] font-bold font-[quick]">#{item.popularity}</p>
                          </div>
                          <div>
                            <p className="text-[#888da8] font-[500]">Market Cap</p>
                            <p className="text-[20px] font-bold font-[quick]">${item.marketcap}B</p>
                          </div>
                        </div>
                        <div>
                          <div>
                            <p className="text-[#888da8] font-[500]">Volume</p>
                            <p className="text-[20px] font-bold font-[quick]">${item.volume}B</p>
                          </div>
                          <div>
                            <p className="text-[#888da8] font-[500]">Circulation Supply</p>
                            <p className="text-[20px] font-bold font-[quick]">${item.supply}B</p>
                          </div>
                        </div>
                      </div>
                      <div>
                        <p className="text-[#888da8]">{item.info}</p>
                        <h2 className="text-[25px] font-bold">Conversion Tables</h2>
                        <div>
                          <div className="flex gap-20">
                            <div>
                              <div>
                                <p className="text-[#888da8] font-[500]">7 days exchange rate</p>
                                <p className="text-[#2ebd85] font-bold text-[19px]">{item.days7}%</p>
                              </div>
                              <div>
                                <p className="text-[#888da8] font-[500]">24-hour exchange rate</p>
                                <p className="text-[#2ebd85] font-bold text-[19px]">{item.days1}%</p>
                              </div>
                            </div>

                            <div>
                              <div>
                                <p className="text-[#888da8] font-[500]">1 month exchange rate</p>
                                <p className="text-[#bd2e2e] font-bold text-[19px]">{item.month1}%</p>
                              </div>
                              <div>
                                <p className="text-[#888da8] font-[500]">3 months exchange rate</p>
                                <p className="text-[#bd2e2e] font-bold text-[19px]">{item.month3}%</p>
                              </div>
                            </div>
                          </div>
                          <p className="text-[#888da8]">{item.other}</p>
                          <p className="text-[#888da8]">{item.otherinfo}</p>
                        </div>
                      </div>
                    </div>
                  )
                })
              }
            </div>
          </div>
        </div>
        <div>
            
        </div>
      </div>
    </div>
  )
}

export default Tether