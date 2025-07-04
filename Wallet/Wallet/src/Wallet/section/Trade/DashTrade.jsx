import { BsArrowDownShort, BsStarFill } from "react-icons/bs"
import { chartMenu, dailysells, infoCrypto, listCripto, newOrder, orderBook, popular, spot, twoelements } from "../../object/myobject"
import { BiDownArrow, BiSearch, BiX } from "react-icons/bi"
import { CgMaximizeAlt, CgMinimizeAlt, CgMore } from "react-icons/cg"
import { TbBasketFilled } from "react-icons/tb"
import { useSelector } from "react-redux"
import { useState } from "react"
import Graphic from "./Graphic"
import { PiWarningCircle } from "react-icons/pi"
import { MdSignalCellular0Bar } from "react-icons/md"

const DashTrade = () => {
  const myvalue = useSelector(state=>state.boolean.boolean)
  const [myid, setMyId] = useState(0)
  const [otherid, setOtherId] = useState(0)
  const [marketId, setMarketId] = useState(0)
  const [counter, setCounter] = useState(0)
  const [spotId, setSpotId] = useState(0)
  const [maximize, setMaximize] = useState(false)
  const increase=()=>{
    if(counter >=0 && counter < listCripto.length){
      setCounter(counter=>counter + 1)
      setOtherId(counter)
    }
    else{
      setOtherId(0)
      setCounter(0)
    }

  }
  const decrease=()=>{
    if(counter >= 0 && counter < 6){
      setCounter(counter =>counter - 1)
      setOtherId(counter)
    }
    else{
      setCounter(5)
    }

  }
  const currency =(amount)=>{
 
    if(String(amount).includes(",")){
      amount = Number(amount.replace(",", "."))
    }
    const pamount = amount 
    const decimals = 2
    const valorFormatado = pamount.toLocaleString('USA', {
      style:"currency",
      currency:"usd",
      min:decimals,
      max:decimals
    })

    return valorFormatado
  }
  return (
    <div className=" my-[40px]">
      <div className="border-b-[1px] px-[2%] border-[#80808058] flex items-center justify-between text-[#888da8] pb-[10px] text-[14px]">
        <div>
          <span>Introducing Caitizen {"("}CATTI{")"} on Wallet LaunchPool...</span>
          <span>{"("}09-13{")"}</span>
        </div>
        <div>
          <span>Wallet Has completed the polygon{"("}MATIC{")"} Token ... </span>
          <span>{"("}09-13{")"}</span>
        </div>
        <div>
          <span>Wallet Will add Polygon {"("}POL{")"} on Earth, Buy crypto...</span><span>(09-13)</span>
        </div>
        <div className="flex items-center gap-[20px]">
          <BsArrowDownShort size={20} className="cursor-pointer"/> 
          <BiX size={20} className="cursor-pointer"/>
        </div>
      </div>
      <div className="flex w-full">
        <div className="basis-[77%]">

          <div className="flex items-center border-[#80808058] border-x-[1px] border-b-[1px] px-[2%] py-[5px] text-[14px] font-[quick]">
            <div className="mr-[10px] border-[#80808058] border-[1px] p-1 rounded-[5px]"><BsStarFill color="gray"/></div>
            <div className="">
              {
                infoCrypto.map((item, index)=>{
                  return(
                    <div key={index} className="flex items-center gap-[13px]">
                      <div>
                        <p className="font-[500] text-[19px]">{item.name}</p>
                        <span>{item.span}</span>
                      </div>
                      <div>
                        <p className="font-[600] text-[19px] text-[red]">{currency(item.price)}</p>
                        <span>{currency(item.price)}</span>
                      </div>
                      <div>
                        <p className="text-[#888da8]">24h Change</p>
                        <span className="text-[#42f542]">{item.change}%</span> 
                      </div>
                      <div>
                        <p className="text-[#888da8]">24h High</p>
                        <span>{item.highPrice}</span>
                      </div>
                      <div>
                        <p className="text-[#888da8]">24Low</p>
                        <span>{item.low}</span>
                      </div>
                      <div>
                        <p className="text-[#888da8]">24h Volume(BTC)</p>
                        <span>{item.volumeBTC}</span>
                      </div>
                      <div>
                        <p className="text-[#888da8]">24h Volume (USDT)</p>
                        <span>{item.volumeUSDT}</span>
                      </div>
                      <div>
                        <p className="text-[#888da8]">Token Tags</p>
                        <div className="flex gap-1">
                        {
                          item.tags.map((items, index)=>{
                            return(
                              <div className={myvalue ?"bg-[#b4a47479] text-[#564113] font-[600] text-[14px] rounded-[5px] " :"bg-[#2e2a1e] text-[#e4ac0d] font-[600] text-[14px] rounded-[5px] "} key={index}>{items}</div>
                            )
                          })
                        }
                        </div>
                      </div>
                    </div>
                  )
                })
              }
            </div>
          </div>
          <div>
            <div className="flex">
              <div className=" py-2 border-x-[1px] border-b-[1px] border-[#80808058] w-[45%]">
                <div className="flex items-center justify-between border-b-[1px] border-[#80808058] px-[6%] py-1">
                  <h2 className="font-[600]">Order Book</h2>
                  <CgMore/>
                </div>
                <div className="">
                  <div className="px-[6%]">
                    <table className="text-[15px] ">
                      <thead>
                        <tr className="text-[#888da8]">
                          <th className="px-[10px]">Price(USDT)</th>
                          <th className="px-[10px]">Amount(BTC)</th>
                          <th className="px-[10px]">Total</th>
                        </tr>
                      </thead>
                      <tbody>
                      {
                        orderBook.map((item, index)=>{
                          return(
                            <tr key={index} className="font-[500]">
                              <td className="text-[red] py-1">{item.price.toFixed(2)}</td>
                              <td className=" py-1">{item.amount}</td>
                              <td className=" py-1">{item.total.toFixed(4)}</td>
                            </tr>
                          )
                        })
                      }
                      </tbody>
                      <div className="flex items-center">
                        <span className="text-[red] flex items-center">{59.310.toFixed(3)},00<BiDownArrow/></span>
                      </div>
                      <tbody>
                        {
                          orderBook.map((item, index)=>{
                            return(
                              <tr key={index}>
                                <td className="text-[#49e749]">{item.price}</td>
                                <td>{item.amount}</td>
                                <td>{item.total}</td>
                              </tr>
                            )
                          })
                        }
                      </tbody>

                    </table>
                    <div className="flex items-center justify-between">
                      <div>B <span className="text-[#49e749]">{90.29}%</span></div>
                      <div className="flex gap-1">
                        <div className="border-[4px] border-[#49e749] w-[100px] rounded-l-[20px]">
                        </div>
                        <div className="border-[4px] border-[red] w-[20px] rounded-r-[20px]">
                        </div>
                      </div>
                      <div><span className="text-[red]">9.71%</span> S</div>
                    </div>
                  </div>
                </div>
              </div>
              <div className="border-b-[1px] border-r-[1px] border-[#80808058]  py-2 w-full">
                <div className="flex justify-between mb-[10px] border-b-[1px] px-[4%] border-[#80808058]">
                  <ul className="flex gap-[20px] font-[600] text-[14px] ">
                    {
                      chartMenu.map((item, index)=>{
                        const showchart = ()=>{
                          setMyId(item.id)
                        }
                        return(
                          <li onClick={showchart} className={myid === item.id ? "cursor-pointer border-b-[4px] border-[#fcd535] transition-[1s] pb-2":"cursor-pointer transition-[1s]"} key={index}>{item.nome}</li>
                        )
                      })
                    }
                  </ul>
                  <div className="flex items-center gap-5">
                    <CgMaximizeAlt onClick={()=>setMaximize(!maximize)} size={18} className="cursor-pointer"/>
                    <TbBasketFilled size={18} className="cursor-pointer"/>
                  </div>
                </div>
                <div className={maximize?"absolute z-10 bg-[#000000a5] w-full top-0 left-0 text-[white]" : ""}>
                  {maximize&&<button className="absolute right-[5%] top-[10%] text-[25px]"><CgMinimizeAlt onClick={()=>setMaximize(false)}/></button>}
                  <div className="px-[2%] border-b-[1px] border-[#80808056]">
                      <Graphic/>
                  </div>
                  <div>
                    <div className="flex items-center">
                      {
                        spot.map((item, index)=>{
                          const pegar =()=>{
                            setSpotId(item.id)
                          }
                          return(
                            <div key={index} className={item.id === spotId? myvalue?"border-t-[2px] border-[#a38d12] py-4 px-7 bg-[#fff] cursor-pointer" :"border-t-[2px] border-[#a38d12] py-4 px-7 bg-[#1e2026] cursor-pointer":" py-4 px-6 cursor-pointer"} onClick={pegar}>
                              <div> {item.nome}</div>
                              
                            </div>
                          )
                      
                        })
                      }
                    </div>
                    <div className={myvalue?"bg-[#fff]" :"bg-[#1e2026] h-full"}>
                      <div className="px-[2%] py-[10px]">
                        <div className="flex justify-between items-center ">
                          <ul className="flex items-center gap-3">
                            <li>Limit</li>
                            <li>Market</li>
                            <li>Stop-limit</li>
                            <PiWarningCircle/>
                          </ul>
                          <ul className="flex items-center gap-3">
                            <li>Transfer</li>
                            <li>Auto-Invest</li>
                            <li>Buy with EUR</li>
                          </ul>
                        </div>
                      </div>
                      <div className="flex gap-[20px] px-[2%]">
                        <div className="basis-[50%]">
                          <div><span className="text-[#888da8] text-[13px]">Avbi</span><span>{}</span></div> 
                          <div>
                            <div className={myvalue?"bg-[#d2d3d5] w-[100%] rounded-[5px] py-2 my-[10px] px-[10px] flex items-center justify-between" :"bg-[#2a2d35] w-[100%] rounded-[5px] py-2 my-[10px] px-[10px] flex items-center justify-between"}>
                              <input placeholder="price"  type="number" id="price1" className="bg-[#00000000]"/>
                              <div className="flex items-center gap-2"><span>59188,06</span> <span>USDT</span></div>
                            </div>
                            <div className={myvalue?"bg-[#d2d3d5] w-[100%] rounded-[5px] py-2 my-[10px] px-[10px] flex items-center justify-between" :"bg-[#2a2d35] w-[100%] rounded-[5px] py-2 my-[10px] px-[10px] flex items-center justify-between"}>
                              <input placeholder="Amount" type="number" id="amount1"  className="bg-[#00000000]"/>
                              <div>BTC</div>
                            </div>
                            <input type="range" id="range" className="w-full"/>
                            <div className={myvalue? "flex items-center justify-between bg-[#d2d3d5] w-full py-2 my-[10px] rounded-[5px] px-[2%]":"flex items-center justify-between bg-[#2a2d35] w-full py-2 my-[10px] rounded-[5px] px-[2%]"}>
                              <input readOnly placeholder="Total" className="bg-[#00000000]"/>
                              <span>USDT</span>
                            </div>
                            <div className="border-b-[1px] border-[#80808056] mb-[10px]">
                              <p className="text-[#888da8] text-[13px]">Max Buy</p>
                              <p className="text-[#888da8] text-[13px]">Est. Fee</p>
                            </div>
                            <div className="flex items-center gap-1 mb-[10px]">
                              <input type="checkbox" id="check" />
                              <span>TP/SL</span>
                            </div>
                            <button className="bg-[#2ebd85] p-1 rounded-[5px] w-full">Buy BTC</button>
                          </div>     
                        </div>
                        <div className="basis-[50%]">
                          <div><span className="text-[#888da8] text-[13px]">Avbi</span><span>{}</span></div>
                          <div>
                            <div className={myvalue?"bg-[#d2d3d5] w-[100%] rounded-[5px] py-2 my-[10px] px-[10px] flex items-center justify-between" :"bg-[#2a2d35] w-[100%] rounded-[5px] py-2 my-[10px] px-[10px] flex items-center justify-between"}>
                              <input placeholder="price" type="number" id="price2"  className="bg-[#00000000]"/>
                              <div className="flex items-center gap-2"><span>59188,06</span> <span>USDT</span></div>
                            </div>
                            <div className={myvalue?"bg-[#d2d3d5] w-[100%] rounded-[5px] py-2 my-[10px] px-[10px] flex items-center justify-between" :"bg-[#2a2d35] w-[100%] rounded-[5px] py-2 my-[10px] px-[10px] flex items-center justify-between"}>
                              <input placeholder="Amount" type="number" id="amount2" className="bg-[#00000000]" />
                              <span>BTC</span>
                            </div>
                            <input type="range" id="range" className="w-full bg-[red]"/>
                            <div className={myvalue? "flex items-center justify-between bg-[#d2d3d5] w-full py-2 my-[10px] rounded-[5px] px-[2%]":"flex items-center justify-between bg-[#2a2d35] w-full py-2 my-[10px] rounded-[5px] px-[2%]"}>
                              <input readOnly placeholder="Total" className="bg-[#00000000]"/>
                              <span>USDT</span>
                            </div>
                            <div className="border-b-[1px] border-[#80808056] mb-[10px]">
                              <p className="text-[#888da8] text-[13px]">Max Buy</p>
                              <p className="text-[#888da8] text-[13px]">Est. Fee</p>
                            </div>
                            <div className="flex items-center gap-1 mb-[10px]">
                              <input type="checkbox" id="check" />
                              <span>TP/SL</span>
                            </div>
                            <button className="bg-[#f6465d] p-1 rounded-[5px] w-full">Sell BTC</button>
                          </div>     
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>


        </div>
        <div className="w-full basis-[23%] py-[10px]">
          <div className="px-[6%]">
            <div className="border-[#80808058] rounded-[5px] border-[1px] flex items-center p-2 w-[300]">
              <BiSearch color="#888da8"/>
              <input placeholder="search" className="bg-[#80808000]"/>
            </div>
            <div className="flex gap-1 items-center justify-center py-[10px]">
              <button className="font-bold text-[19px] mr-[10px]" onClick={decrease}>{"<"}</button>
                  {
                    listCripto.map((item, index)=>{
                      const mine = ()=>{
                        setOtherId(item.id)
                      }
                      return(
                        <div className={otherid === item.id ?"font-[600] border-b-[4px] border-[#fcd535] cursor-pointer text-[14px]" :"font-[600] cursor-pointer text-[14px]"} onClick={mine} key={index}>{item.nome}</div>
                      )
                    })
                  }
              <button onClick={increase} className="font-bold text-[19px] ml-[10px]">{">"}</button>
            </div>
          </div>
          <div className="flex items-center justify-center border-b-[1px] border-[#80808058] pb-2">
            <div>
              <table>
                <thead>
                  <tr className="text-[#888da8] text-[14px] font-[400]">
                    <th className="px-[40px]">Pair</th>
                    <th>Last Price</th>
                    <th>/24h Change</th>
                  </tr>
                </thead>
                <tbody className="text-[14px]">
                  {
                    dailysells.map((item, index)=>{

                      return(
                        <tr key={index} className="text-center font-[600]">
                          <td className="py-1 items-center flex gap-1"><BsStarFill color="#888da8"/>{item.pair}</td>
                          <td>{item.lastprice}</td>
                          <td className="">{item.change24h < 0 ?<span className="text-[red]">{item.change24h}%</span> :<span className="text-[#49e749]">{item.change24h}%</span>}</td>
                        </tr>
                      )
                    })
                  }
                </tbody>
              </table>
            </div>
          </div>
          <div>
            <div className="flex gap-[20px] border-b-[1px] border-[#80808054] py-2 items-center justify-center font-[600]">
              {
                twoelements.map((item, index)=>{
                  const pegar=()=>{
                    setMarketId(item.id)
                  }
                  return(
                    <div key={index} className={marketId === item.id ?"border-b-[4px] border-[#fcd535] rounded-[4px] cursor-pointer" :" cursor-pointer"} onClick={pegar}>{item.nome}</div>
                  )
                })
              }
            </div>
            <div className="border-[#80808056] border-b-[1px] flex justify-center items-center">
              <div>
                <table>
                  <thead>
                    <tr>
                      <th>Price(USDT)</th>
                      <th>Amount(BTC)</th>
                      <th>Time</th>
                    </tr>
                  </thead>
                  <tbody>
                    {
                      newOrder.map((item, index)=>{

                        return(
                          <tr key={index}>
                            <td className="">{item.price < 60000 ?<span className="text-[red]">{item.price.toFixed(2)}</span> :<span className="text-[#49e749]">{item.price.toFixed(2)}</span>}</td>
                            <td>{item.amount}</td>
                            <td>{item.time}</td>
                          </tr>
                        )
                      })
                    }
                  </tbody>
                </table>
              </div>
            </div>
          </div>
                     
        </div>
      </div>
      <div className={myvalue?"bottom-0 fixed flex gap-5 bg-[#fff] w-full" :"bottom-0 fixed flex gap-5 bg-[#181a20] w-full"}>
        <div className="text-[#3dc03d] flex items-center border-r-[2px] border-[#80808056] pr-3">
          <MdSignalCellular0Bar/><span>Stable Connection</span>
        </div>
        <div className="flex border-r-[2px] border-[#80808056]">
          {
            popular.map((item, index)=>{
              return(
                <div className="flex gap-2 mr-[5px]" key={index}>{item.nome} <span>{item.change < 0 ?<span className="text-[red]">{item.change}</span>:<span className="text-[#38b238]">{item.change}</span> }</span></div>
              )
            })
          }
        </div>
      </div>
    </div>
  )
}

export default DashTrade